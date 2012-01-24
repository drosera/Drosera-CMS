<?php

namespace Drosera\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks() .
 * @ORM\Entity(repositoryClass="Drosera\UserBundle\Repository\CredentialRepository")
 */
class Credential
{    
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length="255") 
     */
    protected $name;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="UserGroup", inversedBy="credentials")
     */
    protected $user_group;
    
    
    public function __construct()
    {  
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user_group
     *
     * @param Drosera\UserBundle\Entity\UserGroup $userGroup
     */
    public function setUserGroup(\Drosera\UserBundle\Entity\UserGroup $userGroup)
    {
        $this->user_group = $userGroup;
    }

    /**
     * Get user_group
     *
     * @return Drosera\UserBundle\Entity\UserGroup 
     */
    public function getUserGroup()
    {
        return $this->user_group;
    }
}