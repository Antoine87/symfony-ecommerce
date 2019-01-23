<?php

namespace App\Module\Customer\Query;

use App\Module\Customer\Customer;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;

final class ShoppingCartFromCustomer implements QueryFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Customer $customer)
    {
        $query = $this->entityManager->createQueryBuilder();
        $query = $query
            ->select('c', 'o')
            ->from(Customer::class, 'c')
            ->innerJoin('c.shoppingCart', 'o')
            ->where($query->expr()->eq('c.id', ':customer'))
            ->setParameter('customer', $customer->getId());

        return $query->getQuery()->getResult();
    }
}
