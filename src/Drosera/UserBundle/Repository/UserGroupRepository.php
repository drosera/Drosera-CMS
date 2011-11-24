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
    
    public function getValid(UserInterface $user, $returnQuery = false)
    {        
        $userGroupId = $user->getUserGroup()->getId();
        $qb = $this->createQueryBuilder('ug')->where('ug.time_deleted IS NULL')->orderBy('ug.name', 'ASC');
        
        if ($userGroupId > 1)
           $qb->where('ug.id > 1'); 
        
        if ($returnQuery)
            return $qb;
            
        return $qb->getQuery()->getResults();
    }  
}