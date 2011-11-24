<?php

namespace Drosera\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DashboardController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('DroseraAdminBundle:Dashboard:index.html.twig', array());
    }
}
