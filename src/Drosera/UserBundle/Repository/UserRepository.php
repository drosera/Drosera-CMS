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
    
    public function isUnique(UserInterface $user, $propertyName)
    {
        //var_dump($this->_class); 
        $classMetadata = $this->_em->getClassMetadata($this->_entityName);        
        if (!$classMetadata->hasField($propertyName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->_class, $propertyName));
        }        
        $propertyValue = $classMetadata->getFieldValue($user, $propertyName);
        
        $qb = $this->createQueryBuilder('u')
            ->where('u.time_deleted IS NULL')
            ->andWhere('u.'.$propertyName.' = :username')
            ->setParameter('username', $propertyValue);
            
        if ($this->_em->getUnitOfWork()->getEntityState($user) != UnitOfWork::STATE_NEW) {
          $qb->andWhere('u.id != :user_id')->setParameter('user_id', $user->getId());  
        }
            
        $users = $qb->getQuery()->getResult();
   
        return (boolean) !count($users);
    }      
}