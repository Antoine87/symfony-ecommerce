<?php

namespace App\Module\Product\Query;

use App\Module\Product\Category;
use App\Module\Product\Product;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

final class ProductsFromCategory implements QueryFunction
{
    public const MAX_PER_PAGE = 5;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Category $category, int $page,
                             ?array $features, ?float $lowPrice, ?float $highPrice): Pagerfanta
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from(Product::class, 'p');

        // By category
        $query
            ->addSelect('o', 'r')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.offers', 'o')
            ->leftJoin('p.reviews', 'r')
            ->where($query->expr()->eq('c.id', ':category'))
            ->setParameter('category', $category->getId());

        // By features
        if ($features !== null && \count($features)) {
            $features = \array_values($features);
            $query->addSelect('f')
                ->leftJoin('p.additionalProperty', 'f')
                ->andWhere($query->expr()->in('f.slug', ':features'))
                ->setParameter('features', $features);
        }

        // By prices
        if ($lowPrice !== null) {
            $query->andWhere($query->expr()->gt('o.price', $lowPrice));
        }
        if ($highPrice !== null) {
            $query->andWhere($query->expr()->lt('o.price', $highPrice));
        }

        // Paginate
        $paginator = (new Pagerfanta(new DoctrineORMAdapter($query, true, false)))
            ->setMaxPerPage(self::MAX_PER_PAGE)
            ->setCurrentPage($page);

        return $paginator;
    }
}
