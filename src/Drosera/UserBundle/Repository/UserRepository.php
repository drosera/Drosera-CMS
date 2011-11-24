<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
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
    
      
}