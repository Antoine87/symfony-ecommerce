<?php

namespace App\Module\Product\Query;

use App\Module\Product\Category;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

final class CategoryTree extends NestedTreeRepository implements QueryFunction
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, $entityManager->getClassMetadata(Category::class));
    }

    /**
     * @param Category|null $root
     *
     * @return array
     */
    public function __invoke(Category $root = null): array
    {
        $categories = $this
            ->createQueryBuilder('c')
            ->select(
                'c.id',
                'c.description',
                'c.image',
                'c.name',
                'c.slug',
                'c.lft',
                'c.lvl',
                'c.rgt',
                'c.createdAt',
                'c.updatedAt',
                'COUNT(cp.id) AS total_products'
            )
            ->leftJoin('c.products', 'cp')
            ->groupBy('c.id')
            ->orderBy('c.root, c.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $this->buildTree($categories);
    }
}
