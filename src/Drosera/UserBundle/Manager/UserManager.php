<?php

namespace Drosera\UserBundle\Manager;

use Drosera\UserBundle\Repository\UserRepository;
use Drosera\UserBundle\Entity\UserGroup;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Validator\Constraint;

class UserManager implements UserProviderInterface 
{
    protected $userRepository;
    protected $container;     
    protected $encoderFactory;
    protected $class;
    
    public function __construct(UserRepository $userRepository, ContainerInterface $container, EncoderFactoryInterface $encoderFactory, $class)
    {
       $this->userRepository = $userRepository;
       $this->container = $container; 
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
    
    public function restore(UserInterface $user)
    {
        $user->setTimeTrashed(null);                   
        $this->userRepository->update($user);
    }
    
    public function remove(UserInterface $user, $andFlush = true)
    {
        $time = new \DateTime();
        
        if ($user->getTimeTrashed() === null)
          $user->setTimeTrashed($time);           
        
        $this->userRepository->update($user, $andFlush);
    }
    
    public function removeByUserGroup(UserGroup $userGroup)
    {
        foreach ($userGroup->getUsers() as $user) {
            $this->remove($user, false);
        }          
        
        $this->userRepository->flush();
    }
    
    public function changeUserGroup(array $users, UserGroup $userGroup)
    {
        foreach ($users as $user) {
            $user->setUserGroup($userGroup);
        }          
        
        $this->userRepository->flush();
    }
    
    public function delete(UserInterface $user, $andFlush = true)
    {
        $time = new \DateTime();
        
        if ($user->getTimeTrashed() === null)
          $user->setTimeTrashed($time);  
        
        if ($user->getTimeDeleted() === null)
          $user->setTimeDeleted($time);        
        
        $this->userRepository->update($user, $andFlush);
    }
    
    public function getList($userGroupId = null)
    {
        $withSuperadmins = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userRepository->getList(false, $withSuperadmins, $userGroupId); 
    }
    
    public function getTrashed()
    {
        $withSuperadmins = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userRepository->getList(true, $withSuperadmins); 
    }
    
    public function deleteTrashed()
    {
        $withSuperadmins = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $trashedUsers = $this->userRepository->getList(true, $withSuperadmins);
        
        foreach ($trashedUsers as $user)
           $this->delete($user, false); 
        
        $this->userRepository->flush(); 
    }
    
    public function getById($id)
    {       
        $criteria = array('id' => intval($id), 'time_deleted' => null);
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
    
    public function validateUnique(UserInterface $user, Constraint $constraint)
    {       
        return $this->userRepository->isUnique($user, $constraint->property);
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
        $user = $this->userRepository->loadUserForLogin($username);
        
        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name "%s" was found.', $username));
        }

        return $user;
    }
}
