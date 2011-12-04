<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;

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
    
    public function getValid($withSuperadmin = false, $returnQueryBuilder = false)
    {        
        $qb = $this->createQueryBuilder('ug')->where('ug.time_deleted IS NULL')->orderBy('ug.name', 'ASC');

        if (!$withSuperadmin)
           $qb->where('ug.id > 1'); 
            
        return $returnQueryBuilder ? $qb : $qb->getQuery()->getResults();
    }  
}