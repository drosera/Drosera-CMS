<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Drosera\UserBundle\Form\CredentialType;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CredentialController extends Controller
{   
    /**
     *  @PreAuthorize("hasRole('ROLE_ADMIN')") 
     */
    public function credentialsAction()
    {                 
        $revokeUrl = $this->get('router')->generate('drosera_user_admin_credentials_revoke');
        $grantUrl = $this->get('router')->generate('drosera_user_admin_credentials_grant');

        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new CredentialType($grantUrl, $revokeUrl, $isSuperadmin));
        
        if ($this->get('request')->isXmlHttpRequest())
        {          
            $credentials = $this->container->getParameter('user_bundle.credentials');
            
            $userGroupManager = $this->get('drosera_user.user_group_manager');
            $userGroup = $userGroupManager->getById($this->get('request')->get('user_group_id'));
            $grantedCredentials = $userGroup->getRolesAsName();
            
            return $this->render('DroseraUserBundle:Credential:credentials_settings.html.twig', array(
                'grantedCredentials' => $grantedCredentials, 
                'credentials' => $credentials,
            ));
        } 

        return $this->render('DroseraUserBundle:Credential:credentials.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_ADMIN')") 
     */
    public function grantAction(Request $request)
    {  
        if ($request->getMethod() == 'POST' && $this->get('request')->isXmlHttpRequest())
        {           
            $credentialManager = $this->get('drosera_user.credential_manager');
            $userGroupManager = $this->get('drosera_user.user_group_manager');
            $userGroup = $userGroupManager->getById($this->get('request')->get('user_group_id'));
            $credential = $this->get('request')->get('credential_name');
            
            $credentialManager->grant($userGroup, $credential);
            
            return new Response(json_encode(array('status' => 200)));
        } 
    }
    
    /**
     *  @PreAuthorize("hasRole('ROLE_ADMIN')") 
     */
    public function revokeAction(Request $request)
    {  
        if ($request->getMethod() == 'POST' && $this->get('request')->isXmlHttpRequest())
        {           
            $credentialManager = $this->get('drosera_user.credential_manager');
            $userGroupManager = $this->get('drosera_user.user_group_manager');
            $userGroup = $userGroupManager->getById($this->get('request')->get('user_group_id'));
            $credential = $this->get('request')->get('credential_name');
            
            $credentialManager->revoke($userGroup, $credential);
            
            return new Response(json_encode(array('status' => 200)));
        } 
    }
}
