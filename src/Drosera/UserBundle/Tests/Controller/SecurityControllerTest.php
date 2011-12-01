<?php

namespace Drosera\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {        
        $client = $this->createClient(array('environment' => 'test', 'debug' => false));
        $client->followRedirects(true);

        $serviceContainer = self::$kernel->getContainer();
        $router = $serviceContainer->get('router'); 
        
        $crawler = $client->request('GET', $router->generate('drosera_user_login'));
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('form')->count() > 0);
    }
}
