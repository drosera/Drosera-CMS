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
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $users = $userManager->getList($isSuperadmin);
        
        return $this->render('DroseraUserBundle:User:list.html.twig', array(
            'users' => $users,
            'loggedUser' => $loggedUser
        ));
    }
    
    public function createAction(Request $request)
    {
        $userManager = $this->get('drosera_user.user_manager');
        $user = $userManager->create();
        
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new UserType(array('create'), $isSuperadmin), $user);
        
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
        
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new UserType(array('edit'), $isSuperadmin), $user);
        
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
        
        $user = $userManager->getById($id);
        $userManager->remove($user);
               
        return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
    }   
}
