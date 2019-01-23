<?php

declare(strict_types=1);

namespace App\Module\Order\Service;

use App\Module\Customer\Customer;
use App\Module\Order\Offer;
use App\Module\Order\Order;
use App\Module\Order\Query\GetOffers;
use App\Module\Order\Query\GetOrder;
use App\Utils\Helper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

final class ShoppingCart
{
    private const SESSION_KEY = 'customer_cart';

    private $security;
    private $session;
    private $entityManager;
    private $getOffers;
    private $getOrder;


    public function __construct(
        Security $security,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        GetOffers $getOffers,
        GetOrder $getOrder
    )
    {
        $this->security = $security;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->getOffers = $getOffers;
        $this->getOrder = $getOrder;
    }

    public function getCart(): Order
    {
        $customer = $this->getCustomer();

        if ($customer !== null) {
            $cart = $customer->getShoppingCart();
            if ($cart === null) {
                $cart = $customer->createShoppingCart();
                $this->entityManager->persist($cart);
                $this->entityManager->persist($customer);
                $this->entityManager->flush();
            } else {
                $cart = ($this->getOrder)($cart);
            }
        } else {
            $cart = $this->session->get(self::SESSION_KEY);
            if ($cart === null) {
                $cart = new Order();
                $this->session->set(self::SESSION_KEY, $cart);
            }
        }

        return $cart;
    }

    public function addItem(Offer $offer, int $quantity): Order
    {
        $cart = $this->getCart()->addItem($offer, $quantity);

        $customer = $this->getCustomer();

        if ($customer !== null) {
            $this->entityManager->persist($customer);
            $this->entityManager->flush();
        }

        return $cart;
    }

    public function removeItem(Offer $offer): Order
    {
        $cart = $this->getCart();

        $item = $cart->getItemFromOffer($offer);

        if ($item !== null) {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
        }
        $cart->removeItem($offer);

        return $cart;
    }

    public function mergeCarts(Customer $customer): void
    {
        $sessionCart = $this->session->get(self::SESSION_KEY);

        if ($sessionCart !== null) {
            $sessionCart = $this->_getNewMergedCart($sessionCart);

            $this->getCart()->addOrderItems($sessionCart->getOrderedItems());

            $this->entityManager->persist($customer);
            $this->entityManager->flush();
        }
    }

    private function getCustomer(): ?Customer
    {
        $loggedUser = $this->security->getUser();

        return $loggedUser instanceof Customer ? $loggedUser : null;
    }

    /**
     * Create a new Order with identical OrderItems from the given one but with freshly fetched Offers.
     *
     * This somewhat fishy hack is used to be able to have a non-persisted Order inside the session but with
     * Offers from the db. The EntityManager needs to know the Offers of each OrderItems before persisting it.
     *
     * @param Order $cart
     *
     * @return Order
     */
    private function _getNewMergedCart(Order $cart): Order
    {
        $syncedOffers = ($this->getOffers)($cart->getAcceptedOffers());
        $mappedOffers = Helper::mapEntitiesById($syncedOffers);

        $newCart = new Order();

        /** @var Offer[] $mappedOffers */
        foreach ($cart->getOrderedItems() as $orderedItem) {
            $newCart->addItem(
                $mappedOffers[$orderedItem->getOrderedItem()->getId()->toString()],
                $orderedItem->getOrderQuantity()
            );
        }

        return $newCart;
    }
}
