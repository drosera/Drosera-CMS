<?php

namespace Drosera\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
                $userManager->update($user); 
                $this->get('session')->setFlash('success', 'Uživatel byl úspěšně vytvořen!');  
                return $this->redirect($this->generateUrl('drosera_user_admin_user_list'));
            }
        }
               
        return $this->render('DroseraUserBundle:Credential:credentials.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
