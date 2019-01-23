<?php

declare(strict_types=1);

namespace App\Controller;

use App\Module\Product\Category;
use App\Module\Product\Query\AllFeatures;
use App\Module\Product\Query\CategoryTree;
use App\Module\Product\Query\ProductsFromCategory;
use App\Module\Order\Service\ShoppingCart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Product module controller
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, name="categories_index")
     */
    public function categoriesIndex(
        Request $request,
        ShoppingCart $cart,
        CategoryTree $categories,
        AllFeatures $features
    ): Response
    {
        [$filterFeatures, $filterLowPrice, $filterHighPrice] = $this->filterRequestQuery(
            $request->get('fts'),
            $request->get('lp'),
            $request->get('hp')
        );

        return $this->render('category/index.html.twig', [
            'cart' => $cart->getCart(),
            'categories' => $categories(),
            'features' => $features(),
            'searchParams' => [
                'features' => $filterFeatures,
                'lowPrice' => $filterLowPrice,
                'highPrice' => $filterHighPrice
            ]
        ]);
    }

    /**
     * @Route("/{slug}", defaults={"page": "1"}, methods={"GET"}, name="categories_show")
     * @Route("/{slug}/page/{page<[1-9]\d*>}", methods={"GET"}, name="categories_show_paginated")
     */
    public function categoriesShow(
        Request $request,
        ShoppingCart $cart,
        Category $category,
        CategoryTree $categories,
        AllFeatures $features,
        ProductsFromCategory $products,
        int $page
    ): Response
    {
        [$filterFeatures, $filterLowPrice, $filterHighPrice] = $this->filterRequestQuery(
            $request->get('fts'),
            $request->get('lp'),
            $request->get('hp')
        );

        return $this->render('category/show.html.twig', [
            'cart' => $cart->getCart(),
            'category' => $category,
            'categories' => $categories(),
            'features' => $features(),
            'products' => $products($category, $page, $filterFeatures, $filterLowPrice, $filterHighPrice),
            'searchParams' => [
                'features' => $filterFeatures,
                'lowPrice' => $filterLowPrice,
                'highPrice' => $filterHighPrice
            ]
        ]);
    }

    /**
     * @Route("/product/{slug}", methods={"GET"}, name="products_show")
     */
    public function productsShow(): Response
    {
        return new Response('todo');
    }


    private function filterRequestQuery($features, $lowPrice, $highPrice): array
    {
        $mappedFeatures = [];

        if (!is_array($features) && $features) {
            $features = explode(' ', $features);
            foreach ($features as $feature) {
                $mappedFeatures[$feature] = $feature;
            }
        }

        return [
            $mappedFeatures,
            is_numeric($lowPrice) ? (float)$lowPrice : null,
            is_numeric($highPrice) ? (float)$highPrice : null
        ];
    }
}
