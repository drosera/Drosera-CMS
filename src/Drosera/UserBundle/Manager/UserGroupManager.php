<?php

namespace Drosera\UserBundle\Manager;

use Drosera\UserBundle\Entity\UserGroup;
use Drosera\UserBundle\Repository\UserGroupRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserGroupManager 
{
    protected $userGroupRepository;
    protected $container;
    protected $class;
    
    public function __construct(UserGroupRepository $userGroupRepository, ContainerInterface $container, $class)
    {
       $this->userGroupRepository = $userGroupRepository;
       $this->container = $container; 
       $this->class = $class; 
    }

    public function create()
    {
        $userGroup = new $this->class;
        return $userGroup;
    }
    
    public function update(UserGroup $userGroup)
    {
        $this->userGroupRepository->update($userGroup);
    }
    
    public function delete(UserGroup $userGroup, $andFlush = true)
    {
        $time = new \DateTime();
        
        if ($userGroup->getTimeTrashed() === null)
          $userGroup->setTimeTrashed($time);  
        
        if ($userGroup->getTimeDeleted() === null)
          $userGroup->setTimeDeleted($time);        
        
        $this->userGroupRepository->update($userGroup, $andFlush);
    }
    
    public function restore(UserGroup $userGroup)
    {
        $userGroup->setTimeTrashed(null);                   
        $this->userGroupRepository->update($userGroup);
    }
    
    public function revive(UserGroup $userGroup)
    {
        $userGroup->setTimeTrashed(null); 
        $userGroup->setTimeDeleted(null); 
        $this->userGroupRepository->update($userGroup);
    }
    
    public function remove(UserGroup $userGroup)
    {
        $time = new \DateTime();
        
        if ($userGroup->getTimeTrashed() === null)
          $userGroup->setTimeTrashed($time);           
        
        $this->userGroupRepository->update($userGroup);
    }
    
    public function getById($id)
    {       
        $criteria = array('id' => intval($id), 'time_deleted' => null);
        return $this->userGroupRepository->findOneBy($criteria); 
    }
    
    public function getAll()
    {
        $withSuperadmin = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userGroupRepository->getAll(false, $withSuperadmin); 
    }
    
    public function getTrashed()
    {
        $withSuperadmins = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userGroupRepository->getAll(true, $withSuperadmins); 
    }
    
    public function deleteTrashed()
    {
        $withSuperadmins = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $trashedUserGroups = $this->userGroupRepository->getAll(true, $withSuperadmins);
        
        foreach ($trashedUserGroups as $userGroup)
           $this->delete($userGroup, false); 
        
        $this->userGroupRepository->flush(); 
    }
    
    public function getFilterMenu()
    {
        $withSuperadmin = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userGroupRepository->getFilterMenu($withSuperadmin);
    }
}
