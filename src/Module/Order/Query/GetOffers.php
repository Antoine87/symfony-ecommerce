<?php

namespace App\Module\Order\Query;

use App\Module\Order\Offer;
use App\Module\QueryFunction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

final class GetOffers implements QueryFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Collection<Offer> $offers
     *
     * @return Collection<Offer>
     */
    public function __invoke(Collection $offers): Collection
    {
        if ($offers->isEmpty()) {
            return new ArrayCollection();
        }

        $query = $this->entityManager->createQueryBuilder();
        $query = $query
            ->select('p', 'o')
            ->from(Offer::class, 'o')
            ->leftJoin('o.itemOffered', 'p')
            ->where($query->expr()->in('o.id', ':offers'))
            ->setParameter('offers', $offers);

        return new ArrayCollection($query->getQuery()->getResult());
    }
}
