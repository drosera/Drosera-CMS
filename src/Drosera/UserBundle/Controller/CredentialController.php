<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Drosera\UserBundle\Form\CredentialType;

class CredentialController extends Controller
{
    
    public function credentialsAction(Request $request)
    {        
        $isSuperadmin = $this->get('security.context')->isGranted('ROLE_SUPERADMIN');
        $form = $this->createForm(new CredentialType($isSuperadmin));
        
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $userGroup = $data['user_group'];
                $loggedUser = $this->get('security.context')->getToken()->getUser();
                $aclProvider = $this->get('security.acl.provider');
                
                $oid = new ObjectIdentity('user_class_oid_identifier', 'Drosera\UserBundle\Entity\User');
                $acl = $aclProvider->createAcl($oid);
                $sid = new RoleSecurityIdentity($userGroup->getRoleName());
                $acl->insertClassAce($sid, MaskBuilder::MASK_CREATE);
                
                $this->get('session')->setFlash('success', 'Práva byla úspěšně nastvena!');  
                return $this->redirect($this->generateUrl('drosera_user_admin_credentials'));
            }
        }
        
        var_dump($this->get('security.context')->isGranted('CREATE', ObjectIdentity::fromDomainObject($this->get('security.context')->getToken()->getUser())));
        
        return $this->render('DroseraUserBundle:Credential:credentials.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
