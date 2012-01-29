<?php

namespace Drosera\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Drosera\UserBundle\Entity\Credential;

class CredentialRepository extends EntityRepository
{
    public function update(Credential $credential, $andFlush = true)
    {
        $this->_em->persist($credential);

        if( $andFlush )
        {
            $this->_em->flush();
        }
    }
    
    public function remove(Credential $credential, $andFlush = true)
    {
        $this->_em->remove($credential);

        if( $andFlush )
        {
            $this->_em->flush();
        }
    }
}