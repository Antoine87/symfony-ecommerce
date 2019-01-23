<?php

declare(strict_types=1);

namespace App\Module\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\UuidIdentifiable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A feature of a product.
 *
 * @see http://schema.org/PropertyValue
 *
 * @ORM\Entity
 * @ORM\Table(name="feature")
 *
 * //@ApiResource(iri="http://schema.org/PropertyValue")
 */
class Feature implements UuidIdentifiable
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
     * @var Product[]
     *
     * @ORM\ManyToMany(targetEntity="Product", mappedBy="additionalProperty")
     */
    private $products;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"value"})
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/value")
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $value;


    public function __construct(string $value)
    {
        $this->id = Uuid::uuid4();
        $this->products = new ArrayCollection();
        $this->value = $value;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getProducts(): array
    {
        return $this->products->toArray();
    }
}
