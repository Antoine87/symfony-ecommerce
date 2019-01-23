<?php

namespace App\Module\Order\Query;

use App\Module\Order\Order;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;

final class GetOrder implements QueryFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Order $order
     *
     * @return Order
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(Order $order): Order
    {
        $query = $this->entityManager->createQueryBuilder();
        $query = $query
            ->select('o', 'i', 'offer', 'p')
            ->from(Order::class, 'o')
            ->leftJoin('o.orderedItems', 'i')
            ->leftJoin('i.orderedItem', 'offer')
            ->leftJoin('offer.itemOffered', 'p')
            ->where($query->expr()->eq('o.id', ':order'))
            ->setParameter('order', $order->getId());

        return $query->getQuery()->getSingleResult();
    }
}
