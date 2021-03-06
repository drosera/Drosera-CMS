<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Drosera\UserBundle\Form\UserType;
use Drosera\UserBundle\Form\UserRestoreType;


class UserController extends Controller
{
    
    public function listAction()
    {
        $userGroupId = intval($this->get('request')->get('user_group')); 
        
        $userManager = $this->get('drosera_user.user_manager');
        $loggedUser = $this->get('security.context')->getToken()->getUser();
        $users = $userManager->getList($userGroupId);
        
        return $this->render('DroseraUserBundle:User:list.html.twig', array(
            'users' => $users,
            'loggedUser' => $loggedUser
        ));
    }
    
    public function trashAction()
    {
        $userManager = $this->get('drosera_user.user_manager');
        $users = $userManager->getTrashed();
        
        return $this->render('DroseraUserBundle:User:trash.html.twig', array(
            'users' => $users,
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
                $this->get('session')->setFlash('success', 'Uživatel byl úspěšně vytvořen!');  
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
                $this->get('session')->setFlash('success', 'Uživatel byl úspěšně upraven!');  
                return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:User:edit.html.twig', array(
            'form' => $form->createView(),
            'user' => $user,
        ));
    }
    
    public function emptyTrashAction()
    {
        $userManager = $this->get('drosera_user.user_manager');        
        $userManager->deleteTrashed();
        
        $this->get('session')->setFlash('success', 'Koš byl úspěšně vysypán!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_trash'));
    }
    
    public function preRestoreAction(Request $request)
    {
        $userManager = $this->get('drosera_user.user_manager');
        $user = $userManager->getById($request->get('id'));
        
        $userGroup = $user->getUserGroup();
        if ($userGroup->isAlive()) {
            return $this->forward('DroseraUserBundle:User:restore', array(
                'id' => $request->get('id'),
            ));
        }
        
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new UserRestoreType($isSuperadmin));
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {                
                $data = $form->getData();
                switch ($data['action']) {
                    case 1:
                        $userGroupManager = $this->get('drosera_user.user_group_manager');
                        $userGroupManager->revive($userGroup);
                        break;
                    case 2:
                        $userManager->changeUserGroup(array($user), $data['user_group']);
                        break;
                    default:
                        break;
                }
                
                return $this->forward('DroseraUserBundle:User:restore', array(
                    'id' => $user->getId(),
                ));
            }
        }
        
        return $this->render('DroseraUserBundle:User:restore.html.twig', array(
            'form' => $form->createView(),
            'userGroup' => $userGroup,
            'user' => $user,
        ));
    }
    
    public function restoreAction($id)
    {
        $userManager = $this->get('drosera_user.user_manager');
        
        $user = $userManager->getById($id);
        $userManager->restore($user);
               
        $this->get('session')->setFlash('success', 'Uživatel byl úspěšně obnoven!');
        return $this->redirect($this->generateUrl('drosera_user_admin_user_trash'));
    }
    
    public function removeAction($id)
    {
        $userManager = $this->get('drosera_user.user_manager');
        
        $user = $userManager->getById($id);
        $userManager->remove($user);
        
        $this->get('session')->setFlash('success', 'Uživatel byl přesunut do koše!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
    }
    
    public function deleteAction($id)
    {
        $userManager = $this->get('drosera_user.user_manager');
        
        $user = $userManager->getById($id);
        $userManager->delete($user);
        
        $this->get('session')->setFlash('success', 'Uživatel byl úspěšně odstraněn!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_trash'));
    }
    
    public function filterMenuAction()
    {
        $userManager = $this->get('drosera_user.user_manager');
        $countAll = count($userManager->getList());
        $countTrashed = count($userManager->getTrashed());
        
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroups = $userGroupManager->getFilterMenu();
        
        return $this->render('DroseraUserBundle:User:filterMenu.html.twig', array(
            'countAll' => $countAll,
            'countTrashed' => $countTrashed,
            'userGroups' => $userGroups,
        ));        
    }   
}
