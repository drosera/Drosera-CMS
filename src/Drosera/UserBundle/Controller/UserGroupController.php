<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Drosera\UserBundle\Form\UserGroupType;
use Drosera\UserBundle\Form\UserGroupRemoveType;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use Gedmo\Loggable\Entity\LogEntry as LogEntry;

class UserGroupController extends Controller
{
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_VIEW')") 
     */
    public function listAction() 
    {        
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroups = $userGroupManager->getAll();
        
        return $this->render('DroseraUserBundle:UserGroup:list.html.twig', array(
            'userGroups' => $userGroups,
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_VIEW')") 
     */
    public function detailAction($id)
    {        
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->getById($id);
        
        return $this->render('DroseraUserBundle:UserGroup:detail.html.twig', array(
            'userGroup' => $userGroup,
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_VIEW')") 
     */
    public function trashAction()
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroups = $userGroupManager->getTrashed();
        
        return $this->render('DroseraUserBundle:UserGroup:trash.html.twig', array(
            'userGroups' => $userGroups,
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_CREATE')") 
     */
    public function createAction(Request $request)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->create();
        
        $form = $this->createForm(new UserGroupType(), $userGroup);
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {
                $userGroupManager->update($userGroup); 
                $this->get('session')->setFlash('success', 'Uživatelská skupina byla úspěšně vytvořena!');  
                return $this->redirect($this->generateUrl('drosera_user_admin_user_group_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:UserGroup:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_EDIT')") 
     */
    public function editAction(Request $request)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->getById($request->get('id'));
        
        $form = $this->createForm(new UserGroupType(), $userGroup);
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {
                $userGroupManager->update($userGroup); 
                $this->get('session')->setFlash('success', 'Uživatelská byla úspěšně upravena!');  
                return $this->redirect($this->generateUrl('drosera_user_admin_user_group_list'));
            }
        }
        
        return $this->render('DroseraUserBundle:UserGroup:edit.html.twig', array(
            'form' => $form->createView(),
            'userGroup' => $userGroup,
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_DELETE')") 
     */
    public function emptyTrashAction()
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');        
        $userGroupManager->deleteTrashed();
        
        $this->get('session')->setFlash('success', 'Koš byl úspěšně vysypán!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_group_trash'));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_CREATE')") 
     */
    public function restoreAction($id)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        
        $userGroup = $userGroupManager->getById($id);
        $userGroupManager->restore($userGroup);
               
        $this->get('session')->setFlash('success', 'Uživatelská skupina byla úspěšně obnovena!');
        return $this->redirect($this->generateUrl('drosera_user_admin_user_group_trash'));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_DELETE')") 
     */
    public function preRemoveAction(Request $request)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->getById($request->get('id'));
        
        $userManager = $this->get('drosera_user.user_manager');
        $users = $userManager->getList($userGroup->getId());
        $usersCount = count($users);
        if (!$usersCount) {
            return $this->forward('DroseraUserBundle:UserGroup:remove', array(
                'id' => $request->get('id'),
            ));
        }
        
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new UserGroupRemoveType($userGroup, $isSuperadmin));
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
    
            if ($form->isValid()) {                
                $data = $form->getData();
                $userManager = $this->get('drosera_user.user_manager');
                switch ($data['action']) {
                    case 1:
                        $userManager->removeByUserGroup($userGroup);
                        break;
                    case 2:
                        $userManager->changeUserGroup($users, $data['user_group']);
                        break;
                    default:
                        throw $this->createNotFoundException('Invalid operation!');
                        break;
                }
                
                return $this->forward('DroseraUserBundle:UserGroup:remove', array(
                    'id' => $userGroup->getId(),
                ));
            }
        }
        
        return $this->render('DroseraUserBundle:UserGroup:remove.html.twig', array(
            'form' => $form->createView(),
            'userGroup' => $userGroup,
            'usersCount' => $usersCount,
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_DELETE')") 
     */
    public function removeAction($id)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        
        $userGroup = $userGroupManager->getById($id);
        $userGroupManager->remove($userGroup);
        
        $this->get('session')->setFlash('success', 'Uživatelská skupina byla přesunuta do koše!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_group_list'));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_DELETE')") 
     */
    public function deleteAction($id)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        
        $userGroup = $userGroupManager->getById($id);
        $userGroupManager->delete($userGroup);
        
        $this->get('session')->setFlash('success', 'Uživatelská skupina byla úspěšně odstraněna!');       
        return $this->redirect($this->generateUrl('drosera_user_admin_user_group_trash'));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_VIEW')") 
     */
    public function filterMenuAction($withTrash = true)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $countAll = count($userGroupManager->getAll());
        $countTrashed = $withTrash ? count($userGroupManager->getTrashed()) : 0;
        
        return $this->render('DroseraUserBundle:UserGroup:filterMenu.html.twig', array(
            'countAll' => $countAll,
            'countTrashed' => $countTrashed,
        ));        
    } 
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_EDIT')") 
     */
    public function historyAction($id)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->getById($id);
        $history = $userGroupManager->getHistoryEntries($userGroup);
        
        return $this->render('DroseraUserBundle:UserGroup:history.html.twig', array(
            'userGroup' => $userGroup,
            'history' => $history,
        ));        
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_USER_GROUPS_EDIT')") 
     */
    public function historyDetailAction($id, $version)
    {
        $userGroupManager = $this->get('drosera_user.user_group_manager');
        $userGroup = $userGroupManager->getById($id);
        $historyEntry = $userGroupManager->getHistoryEntry($userGroup, $version);
        
        return $this->render('DroseraUserBundle:UserGroup:historyDetail.html.twig', array(
            'userGroup' => $userGroup,
            'historyEntry' => $historyEntry,
        ));        
    }
}
