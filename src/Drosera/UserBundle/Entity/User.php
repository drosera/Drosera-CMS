<?php
namespace Drosera\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks() 
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="Drosera\UserBundle\Repository\UserRepository")
 * @Gedmo\Loggable(logEntryClass="Drosera\HistoryBundle\Entity\History")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length="255", unique=true)
     * @Gedmo\Versioned 
     */
    protected $username;
    
    /**
     * @ORM\Column(type="string", length="255") 
     */
    protected $password;
    
    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     * @Gedmo\Versioned 
     */
    protected $degree_front;
    
    /**
     * @ORM\Column(type="string", length="255") 
     * @Gedmo\Versioned
     */
    protected $firstname;
    
    /**
     * @ORM\Column(type="string", length="255") 
     * @Gedmo\Versioned
     */
    protected $lastname;
    
    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     * @Gedmo\Versioned 
     */
    protected $degree_behind;
    
    /**
     * @ORM\Column(type="string", length="255", unique=true)
     * @Gedmo\Versioned 
     */
    protected $email;
    
    /**
     * @ORM\Column(type="string", length="255", nullable=true)
     * @Gedmo\Versioned 
     */
    protected $telephone;
    
    /**
     * @ORM\Column(type="string", length="255") 
     */
    protected $salt;
    
    /**
     * @ORM\Column(type="boolean") 
     */
    protected $active;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserGroup", inversedBy="users") 
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user_group;
    
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
    
    
    /******* Not persisted properties *********/
    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var string
     */
    protected $plainPassword;
    
    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @var boolean
     */
    protected $passwordConfirm;
    
    
    public function __construct()
    {
      $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
      $this->active = false;
    }
    
    /**
     * @ORM\prePersist          
     */
    public function setTimeCreatedValue()
    {
        $this->time_created = new \DateTime();
    }
    
    public function getClassName()
    {
        return get_class($this);
    }

    public function __toString()
    {
        return $this->getUsername();
    }
    
    /**
     * Returns the user roles
     *
     * Implements UserInterface
     *
     * @return array The roles
     */
    public function getRoles()
    {
        /*
        $roles = $this->roles;

        foreach ($this->getGroups() as $group) {
            $roles = array_merge($roles, $group->getRoles());
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;

        return array_unique($roles);
        */

        $roles = $this->getUserGroup()->getRoles();
                
        switch ($this->getUserGroup()->getId()) {
            case 1:
                $roles[] = 'ROLE_SUPERADMIN';
                //$roles[] = 'ROLE_IDDQD'; // obejde veskera prava
            case 2:
                $roles[] = 'ROLE_ADMIN';
            default:
                $roles[] = 'ROLE_USER';   
        }
           
        return $roles;
    }
    
    /**
     * Implementation of SecurityUserInterface
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * Removes sensitive data from the user.
     *
     * Implements SecurityUserInterface
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
    
    /**
     * Implementation of SecurityUserInterface.
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @return Boolean
     */
    public function equals(UserInterface $user)
    {
        /*
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }
        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }
        if ($this->usernameCanonical !== $user->getUsernameCanonical()) {
            return false;
        }
        if ($this->isAccountNonExpired() !== $user->isAccountNonExpired()) {
            return false;
        }
        if (!$this->locked !== $user->isAccountNonLocked()) {
            return false;
        }
        if ($this->isCredentialsNonExpired() !== $user->isCredentialsNonExpired()) {
            return false;
        }
        if ($this->enabled !== $user->isEnabled()) {
            return false;
        }

        return true;
        */
        
        if (!$user instanceof User) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }
        
        if ($this->password !== $user->getPassword()) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        $fullname = '';
        $fullname .= $this->getDegreeFront();
		
        if (strlen($fullname))
			$fullname .= ' ';
		$fullname .= $this->getFirstname();
        
        if (strlen($fullname))
			$fullname .= ' ';
		$fullname .= $this->getLastname();

		if ($this->getDegreeBehind())
			$fullname .= ', ' . $this->getDegreeBehind();
		
        return $fullname;
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set degree_front
     *
     * @param string $degreeFront
     */
    public function setDegreeFront($degreeFront)
    {
        $this->degree_front = $degreeFront;
    }

    /**
     * Get degree_front
     *
     * @return string 
     */
    public function getDegreeFront()
    {
        return $this->degree_front;
    }

    /**
     * Set degree_behind
     *
     * @param string $degreeBehind
     */
    public function setDegreeBehind($degreeBehind)
    {
        $this->degree_behind = $degreeBehind;
    }

    /**
     * Get degree_behind
     *
     * @return string 
     */
    public function getDegreeBehind()
    {
        return $this->degree_behind;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * Get telephone
     *
     * @return string 
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Set active
     *
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
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
    
    /**
     * Get plainPassword
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set plain_password
     *
     * @param string $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {       
        $this->plainPassword = $plainPassword;
    }
    
    /**
     * Get passwordConfirm
     *
     * @return string
     */
    public function getPasswordConfirm()
    {
        return $this->passwordConfirm;
    }

    /**
     * Set passwordConfirm
     *
     * @param string $passwordConfirm
     */
    public function setPasswordConfirm($passwordConfirm)
    {       
        $this->passwordConfirm = ($passwordConfirm == $this->plainPassword) ? true : false;
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
}