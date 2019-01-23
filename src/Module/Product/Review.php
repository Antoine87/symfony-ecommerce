<?php

declare(strict_types=1);

namespace App\Module\Product;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\Customer\Customer;
use App\Module\Order\Exception\ReviewRatingOutOfRangeException;
use App\Module\UuidIdentifiable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A review of an item - for example, of a restaurant, movie, or store.
 *
 * @see http://schema.org/Review
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="review",
 *     uniqueConstraints={@UniqueConstraint(columns={"item_reviewed_id", "author_id"})}
 * )
 *
 * //@ApiResource(iri="http://schema.org/Review")
 */
class Review implements UuidIdentifiable
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
     * @var Customer
     *
     * @ORM\ManyToOne(targetEntity="App\Module\Customer\Customer")
     * @ORM\JoinColumn(name="author_id", nullable=false)
     * @ApiProperty(iri="http://schema.org/author")
     */
    private $author;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="reviews")
     * @ORM\JoinColumn(name="item_reviewed_id", nullable=false)
     * @ApiProperty(iri="http://schema.org/itemReviewed")
     */
    private $itemReviewed;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @ApiProperty(iri="http://schema.org/reviewBody")
     * @Assert\Type(type="string")
     */
    private $reviewBody;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ApiProperty(iri="http://schema.org/reviewRating")
     * @Assert\Type(type="integer")
     */
    private $reviewRating;

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


    private function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public static function create(Customer $author, Product $itemReviewed, string $reviewBody, int $reviewRating): self
    {
        $review = new self();
        $review->author = $author;
        $review->itemReviewed = $$itemReviewed;
        $review->reviewBody = $reviewBody;

        if ($reviewRating < 0 || $reviewRating > 5) {
            throw new ReviewRatingOutOfRangeException('Review rating must be between 0 and 5');
        }
        $review->reviewRating = $reviewRating;

        return $review;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getAuthor(): Customer
    {
        return $this->author;
    }

    public function getItemReviewed(): Product
    {
        return $this->itemReviewed;
    }

    public function getReviewBody(): string
    {
        return $this->reviewBody;
    }

    public function getReviewRating(): int
    {
        return $this->reviewRating;
    }


    /**
     * @internal
     *
     * @todo generate multi-column unique contraint with alice ?
     */
    public static function _forAliceDoNotUseInvalidState(string $reviewBody, int $reviewRating): self
    {
        $review = new self();
        $review->reviewBody = $reviewBody;

        if ($reviewRating < 0 || $reviewRating > 5) {
            throw new ReviewRatingOutOfRangeException('Review rating must be between 0 and 5');
        }
        $review->reviewRating = $reviewRating;

        return $review;
    }

    /**
     * @internal
     */
    public function _forAliceDoNotUseSetAuthor(Customer $customer): self
    {
        $this->author = $customer;

        return $this;
    }

    /**
     * @internal
     */
    public function _forAliceDoNotUseSetProduct(Product $product): self
    {
        $this->itemReviewed = $product;

        return $this;
    }
}
