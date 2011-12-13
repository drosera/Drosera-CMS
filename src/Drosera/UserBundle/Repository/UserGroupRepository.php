<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\UnitOfWork;
use Drosera\UserBundle\Entity\UserGroup;

class UserGroupRepository extends EntityRepository
{
    public function update(UserGroup $userGroup, $andFlush = true)
    {
        $this->_em->persist($userGroup);

        if( $andFlush )
        {
            $this->_em->flush();
        }
    } 
    
    public function flush()
    {
        $this->_em->flush();        
    }
    
    public function getAll($trashed = false, $withSuperadmin = false, $returnQueryBuilder = false)
    {        
        $qb = $this->createQueryBuilder('ug')->where('ug.time_deleted IS NULL')->orderBy('ug.name', 'ASC');

        if ($trashed)    
            $qb->andWhere('ug.time_trashed IS NOT NULL');
        else  
            $qb->andWhere('ug.time_trashed IS NULL');
        
        if (!$withSuperadmin)
           $qb->where('ug.id > 1'); 
            
        return $returnQueryBuilder ? $qb : $qb->getQuery()->getResult();
    }
    
    public function getFilterMenu($withSuperadmin = false)
    {        
        $query = 'SELECT ug, count(u.id) AS countUsers 
                  FROM DroseraUserBundle:UserGroup ug 
                  JOIN ug.users u 
                  WHERE u.time_deleted IS NULL 
                  AND u.time_trashed IS NULL
                  AND ug.time_deleted IS NULL';
        
        if (!$withSuperadmin)
            $query .= ' AND ug.id != 1';
        
        $query .= ' GROUP BY ug.id';
        
        $q = $this->_em->createQuery($query); 
            
        return $q->getResult();
    } 
    
    public function isUnique(UserGroup $userGroup, $propertyName)
    {
        $classMetadata = $this->_em->getClassMetadata($this->_entityName);        
        if (!$classMetadata->hasField($propertyName)) {
            throw new \InvalidArgumentException(sprintf('The "%s" class metadata does not have any "%s" field or association mapping.', $this->_class, $propertyName));
        }        
        $propertyValue = $classMetadata->getFieldValue($userGroup, $propertyName);
        
        $qb = $this->createQueryBuilder('ug')
            ->andWhere('ug.'.$propertyName.' = :value')
            ->setParameter('value', $propertyValue);
            
        if ($this->_em->getUnitOfWork()->getEntityState($userGroup) != UnitOfWork::STATE_NEW) {
          $qb->andWhere('ug.id != :id')->setParameter('id', $userGroup->getId());  
        }
            
        $users = $qb->getQuery()->getResult();
   
        return (boolean) !count($users);
    }
}