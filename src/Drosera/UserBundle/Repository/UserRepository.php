<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends EntityRepository
{
    public function update(UserInterface $user, $andFlush = true)
    {
        $this->_em->persist($user);

        if( $andFlush )
        {
            $this->_em->flush();
        }
    }
    
    public function flush()
    {
        $this->_em->flush();        
    }
    
    public function remove(UserInterface $user)
    {
        $this->_em->remove($user);
        $this->_em->flush();
    }
    
    public function getList($trashed = false, $withSuperadmins = false, $userGroupFilter = null)
    {          
       // snad se to takhle chova jako eager join
       $qb = $this->createQueryBuilder('u', 'ug')
            ->select('u', 'ug')
            ->join('u.user_group', 'ug')
            ->where('u.time_deleted IS NULL')
            ->AndWhere('ug.time_deleted IS NULL');
            
        if ($trashed)    
            $qb->andWhere('u.time_trashed IS NOT NULL');
        else  
            $qb->andWhere('u.time_trashed IS NULL');
         
        if (!$withSuperadmins)    
            $qb->andWhere('ug.id != 1');
            
        if ($userGroupFilter)    
            $qb->andWhere('ug.id = :user_group')
            ->setParameter('user_group', $userGroupFilter);
                        
        return $qb->getQuery()->getResult();
    }  
    
    public function isUnique(UserInterface $user, $propertyName)
    {
        $classMetadata = $this->_em->getClassMetadata($this->_entityName);        
        if (!$classMetadata->hasField($propertyName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->_class, $propertyName));
        }        
        $propertyValue = $classMetadata->getFieldValue($user, $propertyName);
        
        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.'.$propertyName.' = :value')
            ->setParameter('value', $propertyValue);
            
        if ($this->_em->getUnitOfWork()->getEntityState($user) != UnitOfWork::STATE_NEW) {
          $qb->andWhere('u.id != :user_id')->setParameter('user_id', $user->getId());  
        }
            
        $users = $qb->getQuery()->getResult();
   
        return (boolean) !count($users);
    }      
}