<?php

namespace App\Module\Product\Query;

use App\Module\Product\Feature;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;

final class AllFeatures implements QueryFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Feature[]
     */
    public function __invoke(): array
    {
        $query = $this->entityManager->createQueryBuilder();
        $query = $query
            ->select('f', 'p')
            ->from(Feature::class, 'f')
            ->leftJoin('f.products', 'p');

        return $query->getQuery()->getResult();
    }
}
