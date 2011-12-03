<?php

namespace Drosera\UserBundle\Manager;

use Drosera\UserBundle\Entity\User;
use Drosera\UserBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserManager implements UserProviderInterface 
{
    protected $userRepository;    
    protected $encoderFactory;
    protected $class;
    
    public function __construct(UserRepository $userRepository, EncoderFactoryInterface $encoderFactory, $class)
    {
       $this->userRepository = $userRepository; 
       $this->encoderFactory = $encoderFactory;
       $this->class = $class; 
    }

    public function create()
    {
        $user = new $this->class;
        return $user;
    }
    
    public function update(UserInterface $user)
    {
        $this->updatePassword($user);
        $this->userRepository->update($user);
    }
    
    public function getList()
    {
        $criteria = array('time_deleted' => null);
        return $this->userRepository->findBy($criteria); 
    }
    
    public function getById($id)
    {
        if (!$id = intval($id))
            return null;
       
        $criteria = array('id' => $id, 'time_deleted' => null);
        return $this->userRepository->findOneBy($criteria); 
    }
    
    public function updatePassword(UserInterface $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }
    
    public function validateUniqueUsername(UserInterface $user)
    {       
        return $this->userRepository->isUnique($user);
    }
    
    protected function getEncoder(UserInterface $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }
    
    /**
     * Implements UserProviderInterface
     */    
    public function supportsClass($class)
    {
        return $class === $this->class;
    }
    
    /**
     * Implements UserProviderInterface
     */ 
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof $this->class) {
            throw new UnsupportedUserException('Account is not supported.');
        }

        return $this->loadUserByUsername($user->getUsername());
    }
    
    /**
     * Implements UserProviderInterface
     */ 
    public function loadUserByUsername($username)
    {
        $criteria = array('username' => $username, 'active' => true, 'time_deleted' => null);
        $user = $this->userRepository->findOneBy($criteria);
        
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }
}
