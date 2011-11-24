<?php

namespace Drosera\AdminBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin');

        $this->assertTrue($crawler->filter('html:contains("Nástěnka")')->count() > 0);
    }
}
