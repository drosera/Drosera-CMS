<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

use Drosera\UserBundle\Entity\User;
use Drosera\UserBundle\Form\UserType;


class UserController extends Controller
{
    
    public function listAction()
    {
        $userManager = $this->get('drosera_user.user_manager');
        $users = $userManager->getList();
        
        return $this->render('DroseraUserBundle:User:list.html.twig', array(
            'users' => $users,
        ));
    }
    
    public function createAction(Request $request)
    {
        $userManager = $this->get('drosera_user.user_manager');
        $user = $userManager->create();
        
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserType(array('create'), $loggedUser), $user);
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {
                $userManager->update($user);   
                return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:User:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    public function editAction(Request $request)
    {
        $userManager = $this->get('drosera_user.user_manager');
        $user = $userManager->getById($request->get('id'));
        
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserType(array('edit'), $loggedUser), $user);
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {
                $userManager->update($user);   
                return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:User:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
    
    public function deleteAction($id)
    {
        $userManager = $this->get('drosera_user.user_manager');
        $user = $userManager->getById($request->get('id'));
        
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(new UserType($loggedUser), $user);
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {
                $userManager->update($user);   
                return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:User:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }   
}
