<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class PostControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Test',
            'PHP_AUTH_PW' => 'test',
        ));

        $crawler = $client->request('GET', '/api/posts');

        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));
    }

    public function testGet(){
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'Test',
            'PHP_AUTH_PW' => 'test',
        ));

        $crawler = $client->request('GET', '/api/posts/1');

        $this->assertSame('application/json', $client->getResponse()->headers->get('Content-Type'));
    }
}
