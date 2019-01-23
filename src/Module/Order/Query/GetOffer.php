<?php

namespace App\Module\Order\Query;

use App\Module\Order\Offer;
use App\Module\QueryFunction;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

final class GetOffer implements QueryFunction
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Offer|UuidInterface $offer
     *
     * @return Offer
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke($offer): Offer
    {
        if ($offer instanceof Offer) {
            $offerId = $offer->getId();
        } else if ($offer instanceof UuidInterface) {
            $offerId = $offer->toString();
        } else {
            throw new \InvalidArgumentException('Offer must be an Order entity or an Uuid.');
        }

        $query = $this->entityManager->createQueryBuilder();
        $query = $query
            ->select('p', 'o')
            ->from(Offer::class, 'o')
            ->leftJoin('o.itemOffered', 'p')
            ->where($query->expr()->eq('o.id', ':offer'))
            ->setParameter('offer', $offerId);

        return $query->getQuery()->getSingleResult();
    }
}
