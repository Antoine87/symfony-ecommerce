<?php

declare(strict_types=1);

namespace App\Controller;

use App\Module\Order\Offer;
use App\Module\Order\Query\GetOffer;
use App\Module\Order\Service\ShoppingCart;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Order module controller
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/cart", name="shopping_cart_show")
     */
    public function showCart(ShoppingCart $cart): Response
    {
        return $this->render('order/show_cart.html.twig', [
            'cart' => $cart->getCart()
        ]);
    }

    /**
     * @Route("/cart/add-item/{offer}", name="shopping_cart_add_item")
     *
     * @throws \Exception
     */
    public function addCartItem(Request $request, GetOffer $getOffer, ShoppingCart $cartService): JsonResponse
    {
        $quantity = $request->request->get('quantity');

        if (!\is_numeric($quantity)) {
            throw new BadRequestHttpException('Missing or invalid quantity.');
        }

        $cart = $cartService->addItem(
            $getOffer(Uuid::fromString($request->get('offer'))),
            (int)$quantity
        );

        return $this->json([
            'items' => $cart->getItemsCount()
        ]);
    }

    /**
     * @Route("/cart/remove-item/{offer}", name="shopping_cart_remove_item")
     */
    public function removeCartItem(Offer $offer, ShoppingCart $cartService): JsonResponse
    {
        $cart = $cartService->removeItem($offer);

        return $this->json([
            'items' => $cart->getItemsCount()
        ]);
    }
}
