<?php

declare(strict_types=1);

namespace App\Module\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\UuidIdentifiable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * The mailing address.
 *
 * @see http://schema.org/PostalAddress
 *
 * @ORM\Entity
 * @ORM\Table(name="billing_address")
 *
 * //@ApiResource(iri="http://schema.org/PostalAddress")
 */
class BillingAddress implements UuidIdentifiable
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
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/addressCountry")
     * @Assert\Type(type="string")
     */
    private $addressCountry;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $addressCity;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/postalCode")
     * @Assert\Type(type="string")
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/streetAddress")
     * @Assert\Type(type="string")
     */
    private $streetAddress;


    public function __construct(
        string $addressCountry,
        string $addressCity,
        string $postalCode,
        string $streetAddress
    )
    {
        $this->id = Uuid::uuid4();
        $this->addressCountry = $addressCountry;
        $this->addressCity = $addressCity;
        $this->postalCode = $postalCode;
        $this->streetAddress = $streetAddress;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
