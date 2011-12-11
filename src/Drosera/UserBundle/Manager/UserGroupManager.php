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
    
    public function getAll()
    {
        $withSuperadmin = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userGroupRepository->getAll($withSuperadmin); 
    }
    
    public function getFilterMenu()
    {
        $withSuperadmin = $this->container->get('security.context')->isGranted('ROLE_SUPERADMIN');
        return $this->userGroupRepository->getFilterMenu($withSuperadmin);
    }
}
