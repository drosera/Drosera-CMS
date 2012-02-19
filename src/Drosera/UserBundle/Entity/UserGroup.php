<?php

namespace Drosera\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks() 
 * @ORM\Entity(repositoryClass="Drosera\UserBundle\Repository\UserGroupRepository") 
 * @ORM\Table(name="user_group")
 * @Gedmo\Loggable(logEntryClass="Drosera\HistoryBundle\Entity\History")
 */
class UserGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Gedmo\Versioned
     * @ORM\Column(type="string", length="255", unique=true) 
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="user_group")
     */
    protected $users;
    
    /**
     * @ORM\OneToMany(targetEntity="Credential", mappedBy="user_group")
     */
    protected $credentials;
    
    /**
     * @ORM\Column(type="datetime") 
     */
    protected $time_created;
    
    /**
     * @ORM\Column(type="datetime", nullable=true) 
     */
    protected $time_trashed;
    
    /**
     * @ORM\Column(type="datetime", nullable=true) 
     */
    protected $time_deleted;
    
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->credentials = new ArrayCollection();
        
    }

    public function __toString()
    {
        return $this->getName();
    }
    
    public function isSystem()
    {
        return $this->getId() <= 3;
    }
    
    public function getClassName()
    {
        return get_class($this);
    }
    
    public function isAlive()
    {
        return $this->getTimeDeleted() === null && $this->getTimeTrashed() === null;
    }
    
    public function getRoles()
    {
        $credentials = $this->getCredentials();
        $roles = array();
        
        foreach ($credentials as $cred) {
            $roles[] = 'ROLE_'.strtoupper($cred->getName());
        }
        
        return $roles;
    }
    
    public function getRolesAsName()
    {
        $credentials = $this->getCredentials();
        $roles = array();
        
        foreach ($credentials as $cred) {
            $roles[] = $cred->getName();
        }
        
        return $roles;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * @ORM\prePersist          
     */
    public function setTimeCreatedValue()
    {
        $this->time_created = new \DateTime();
    }

    /**
     * Set time_created
     *
     * @param datetime $timeCreated
     */
    public function setTimeCreated($timeCreated)
    {
        $this->time_created = $timeCreated;
    }

    /**
     * Get time_created
     *
     * @return datetime 
     */
    public function getTimeCreated()
    {
        return $this->time_created;
    }

    /**
     * Set time_deleted
     *
     * @param datetime $timeDeleted
     */
    public function setTimeDeleted($timeDeleted)
    {
        $this->time_deleted = $timeDeleted;
    }

    /**
     * Get time_deleted
     *
     * @return datetime 
     */
    public function getTimeDeleted()
    {
        return $this->time_deleted;
    }

    /**
     * Add users
     *
     * @param Drosera\UserBundle\Entity\User $users
     */
    public function addUser(\Drosera\UserBundle\Entity\User $user)
    {
        $this->users[] = $user;
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

    /**
     * Set time_trashed
     *
     * @param datetime $timeTrashed
     */
    public function setTimeTrashed($timeTrashed)
    {
        $this->time_trashed = $timeTrashed;
    }

    /**
     * Get time_trashed
     *
     * @return datetime 
     */
    public function getTimeTrashed()
    {
        return $this->time_trashed;
    }

    /**
     * Add credentials
     *
     * @param Drosera\UserBundle\Entity\Credential $credentials
     */
    public function addCredential(\Drosera\UserBundle\Entity\Credential $credentials)
    {
        $this->credentials[] = $credentials;
    }

    /**
     * Get credentials
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}