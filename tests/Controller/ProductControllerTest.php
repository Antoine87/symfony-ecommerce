<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Module\Product\Query\ProductsFromCategory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testCategoriesShow()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/c/industrial');
        $this->assertCount(ProductsFromCategory::MAX_PER_PAGE, $crawler->filter('article.card-product'));
    }

    public function testCategoriesShowPaginated()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/c/industrial/page/4');
        $this->assertCount(2, $crawler->filter('article.card-product'));
    }
}
