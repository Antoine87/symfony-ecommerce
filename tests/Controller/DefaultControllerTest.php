<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertSame(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertSame($crawler->getUri().'c', $response->getTargetUrl());
    }

    /**
     * @dataProvider getPublicUrls
     */
    public function testPublicUrls(string $url)
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function getPublicUrls()
    {
        yield ['/c'];
        yield ['/login'];
        yield ['/c/electronics'];
        yield ['/c/electronics/page/4'];
    }
}
