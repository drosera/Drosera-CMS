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
    
    public function isUnique(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.time_deleted IS NULL')
            ->andWhere('u.username = :username')
            ->setParameter('username', $user->getUsername());
            
        if ($this->_em->getUnitOfWork()->getEntityState($user) != UnitOfWork::STATE_NEW) {
          $qb->andWhere('u.id != :user_id')->setParameter('user_id', $user->getId());  
        }
            
        $users = $qb->getQuery()->getResult();
   
        return (boolean) !count($users);
    }      
}