<?php

declare(strict_types=1);

namespace App\Module\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\Product\Product;
use App\Module\UuidIdentifiable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An offer to transfer some rights to an item or to provide a service — for example, an offer to sell tickets to an
 * event, to rent the DVD of a movie, to stream a TV show over the internet, to repair a motorcycle, or to loan a book.
 *
 * @see http://schema.org/Offer
 *
 * @ORM\Entity
 * @ORM\Table(name="offer")
 *
 * //@ApiResource(iri="http://schema.org/Offer")
 */
class Offer implements UuidIdentifiable
{
    /**
     * @var UuidInterface
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\Column(type="uuid")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var Product the item being offered
     *
     * @ORM\ManyToOne(targetEntity="App\Module\Product\Product", inversedBy="offers")
     * @ORM\JoinColumn(nullable=false)
     * @ApiProperty(iri="http://schema.org/itemOffered")
     */
    private $itemOffered;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     * @ApiProperty(iri="http://schema.org/price")
     * @Assert\Type(type="float")
     * @Assert\NotNull
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/priceCurrency")
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $priceCurrency;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @ApiProperty(iri="http://schema.org/validFrom")
     * @Assert\DateTime
     */
    private $validFrom;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @ApiProperty(iri="http://schema.org/validThrough")
     * @Assert\DateTime
     */
    private $validThrough;


    public function __construct(Product $itemOffered, float $price, string $currency)
    {
        $this->id = Uuid::uuid4();
        $this->itemOffered = $itemOffered;
        $this->price = $price;
        $this->priceCurrency = $currency;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function getItemOffered(): Product
    {
        return $this->itemOffered;
    }
}
