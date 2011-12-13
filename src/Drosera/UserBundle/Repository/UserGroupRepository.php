<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
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
}