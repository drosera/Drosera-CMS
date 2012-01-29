<?php

namespace Drosera\UserBundle\Manager;

use Drosera\UserBundle\Repository\CredentialRepository;
use Drosera\UserBundle\Entity\UserGroup;
use Drosera\UserBundle\Entity\Credential;

class CredentialManager 
{
    protected $credentialRepository;
    
    public function __construct(CredentialRepository $credentialRepository)
    {
       $this->credentialRepository = $credentialRepository;
    }

    public function grant(UserGroup $userGroup, $credentialName)
    {
        $credential = new Credential();
        $credential->setName($credentialName);
        $credential->setUserGroup($userGroup);
        $userGroup->addCredential($credential);
        
        try {
            $this->credentialRepository->update($credential);
        } catch ( \PDOException $e) {
            if( $e->getCode() !== '23000' ) {
                throw $e;
            }
        }
    }
    
    public function revoke(UserGroup $userGroup, $credentialName)
    {                  
        foreach ($userGroup->getCredentials() as $credential) {
            if ($credentialName == $credential->getName()) {
                $userGroup->getCredentials()->removeElement($credential);
                $this->credentialRepository->remove($credential);
                break;
            }
        } 
    }
}
