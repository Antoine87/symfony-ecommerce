<?php

declare(strict_types=1);

namespace App\Module\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\Order\Offer;
use App\Module\UuidIdentifiable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Any offered product or service.
 *
 * @see http://schema.org/Product
 *
 * @ORM\Entity
 * @ORM\Table(name="product")
 *
 * //@ApiResource(iri="http://schema.org/Product")
 * @Vich\Uploadable
 */
class Product implements UuidIdentifiable
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
     * @var Feature[]
     *
     * @ORM\ManyToMany(targetEntity="Feature", inversedBy="products")
     * @ApiProperty(iri="http://schema.org/additionalProperty")
     */
    private $additionalProperty;

    /**
     * @var Category[]
     *
     * @ORM\ManyToMany(targetEntity="Category", mappedBy="products")
     * @ApiProperty(iri="http://schema.org/category")
     */
    private $categories;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @ApiProperty(iri="http://schema.org/description")
     * @Assert\Type(type="string")
     */
    private $description;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/image")
     */
    private $image;

    /**
     * @var File|null
     *
     * @Vich\UploadableField(
     *     mapping="product_images",
     *     fileNameProperty="image"
     * )
     */
    private $imageFile;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/name")
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $name;

    /**
     * @var Offer[]
     *
     * @ORM\OneToMany(targetEntity="App\Module\Order\Offer", mappedBy="itemOffered")
     * @ApiProperty(iri="http://schema.org/offers")
     */
    private $offers;

    /**
     * @var Review[]
     *
     * @ORM\OneToMany(targetEntity="Review", mappedBy="itemReviewed")
     * @ApiProperty(iri="http://schema.org/review")
     */
    private $reviews;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", unique=true, nullable=true)
     * @ApiProperty(iri="http://schema.org/sku")
     */
    private $sku;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=128, unique=true)
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Type(type="string")
     */
    private $valueProposition;

    /**
     * @var \DateTimeInterface|null
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface|null
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    public function __construct(string $name)
    {
        $this->id = Uuid::uuid4();
        $this->name = $name;
        $this->categories = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->additionalProperty = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getValueProposition(): ?string
    {
        return $this->valueProposition;
    }

    public function setValueProposition(string $valueProposition): self
    {
        $this->valueProposition = $valueProposition;

        return $this;
    }

    public function getOffers()
    {
        return $this->offers;
    }

    /**
     * @return Category[]|Collection<Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeProduct($this);
        }

        return $this;
    }

    /**
     * @return Review[]|Collection<Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    /**
     * @return Feature[]
     */
    public function getFeatures(): array
    {
        return $this->additionalProperty->toArray();
    }

    public function addFeature(Feature $additionalProperty): self
    {
        if (!$this->additionalProperty->contains($additionalProperty)) {
            $this->additionalProperty[] = $additionalProperty;
        }

        return $this;
    }

    public function removeFeature(Feature $additionalProperty): self
    {
        if ($this->additionalProperty->contains($additionalProperty)) {
            $this->additionalProperty->removeElement($additionalProperty);
        }

        return $this;
    }
}
