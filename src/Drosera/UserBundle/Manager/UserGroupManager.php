<?php

namespace Drosera\UserBundle\Manager;

use Drosera\UserBundle\Entity\UserGroup;
use Drosera\UserBundle\Repository\UserGroupRepository;
use Gedmo\Loggable\Entity\Repository\LogEntryRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserGroupManager 
{
    protected $userGroupRepository;
    protected $historyRepository;
    protected $container;
    protected $class;
    
    public function __construct(UserGroupRepository $userGroupRepository, LogEntryRepository $historyRepository, ContainerInterface $container, $class)
    {
       $this->userGroupRepository = $userGroupRepository;
       $this->historyRepository = $historyRepository;
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
        $userGroup = $this->userGroupRepository->findOneBy($criteria); 
        if (!$userGroup)
            throw new NotFoundHttpException(sprintf('No user group with id "%s" was found.', $id));
        return $userGroup;
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
    
    public function getHistoryEntries(UserGroup $userGroup)
    {
        return $this->historyRepository->getLogEntries($userGroup);     
    }
    
    public function getHistoryEntry(UserGroup $userGroup, $version)
    {
        $entries = $this->historyRepository->getLogEntries($userGroup); 
        return $entries[0];
    }
}
