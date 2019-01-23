<?php

declare(strict_types=1);

namespace App\Module\Order;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\Customer\Customer;
use App\Module\Order\Exception\ItemNotFoundException;
use App\Module\Order\Exception\UnauthorizedOrderModificationException;
use App\Module\UuidIdentifiable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An order is a confirmation of a transaction (a receipt), which can contain multiple line items,
 * each represented by an Offer that has been accepted by the customer.
 *
 * @see http://schema.org/Order
 *
 * @ORM\Entity
 * @ORM\Table(name="order")
 *
 * //@ApiResource(iri="http://schema.org/Order")
 */
class Order implements UuidIdentifiable
{
    public const STATUS_IN_CART = 'IN_CART';
    public const STATUS_PLACED = 'PLACED';

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
     * @var BillingAddress|null
     *
     * @ORM\OneToOne(targetEntity="BillingAddress")
     * @ApiProperty(iri="http://schema.org/billingAddress")
     */
    private $billingAddress;

    /**
     * @var Customer|null
     *
     * @ORM\ManyToOne(targetEntity="App\Module\Customer\Customer", inversedBy="orders")
     * @ApiProperty(iri="http://schema.org/customer")
     */
    private $customer;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @ApiProperty(iri="http://schema.org/orderDate")
     * @Assert\DateTime
     */
    private $orderDate;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/orderStatus")
     * @Assert\Type(type="string")
     */
    private $orderStatus;

    /**
     * @var OrderItem[]|Collection<OrderItem>
     *
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"persist"})
     * @ApiProperty(iri="http://schema.org/orderedItem")
     */
    private $orderedItems;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     * @ApiProperty(iri="http://schema.org/paymentMethod")
     * @Assert\Type(type="string")
     */
    private $paymentMethod;

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


    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->orderedItems = new ArrayCollection();
    }

    public static function fromCustomer(Customer $customer): Order
    {
        $order = new self();
        $order->customer = $customer;

        return $order;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function isPlaced(): bool
    {
        return $this->orderStatus === self::STATUS_PLACED;
    }

    public function getTotalPrice(): float
    {
        $total = 0.0;

        foreach ($this->orderedItems as $orderItem) {
            $total += $orderItem->getOrderQuantity() * $orderItem->getOrderedItem()->getPrice();
        }

        return $total;
    }

    public function getAcceptedOffers(): Collection
    {
        $acceptedOffers = new ArrayCollection();

        foreach ($this->getOrderedItems() as $orderItem) {
            $acceptedOffers->add($orderItem->getOrderedItem());
        }

        return $acceptedOffers;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderedItems(): array
    {
        return $this->orderedItems->toArray();
    }

    public function getItemsCount(): int
    {
        return $this->orderedItems->count();
    }

    public function addItem(Offer $offer, int $quantity): self
    {
        foreach ($this->orderedItems as $orderItem) {
            if ($orderItem->getOrderedItem()->getId()->equals($offer->getId())) {
                $orderItem->addQuantity($quantity);
                return $this;
            }
        }
        $this->orderedItems[] = new OrderItem($this, $offer, $quantity);

        return $this;
    }

    /**
     * @param Offer $offer
     *
     * @return Order
     * @throws UnauthorizedOrderModificationException
     */
    public function removeItem(Offer $offer): self
    {
        if ($this->isPlaced()) {
            throw new UnauthorizedOrderModificationException('Cannot remove item from this already placed order.');
        }

        foreach ($this->orderedItems as $key => $orderedItem) {
            if ($orderedItem->getOrderedItem()->getId()->equals($offer->getId())) {
                $this->orderedItems->remove($key);
                break;
            }
        }

        return $this;
    }

    /**
     * @param Offer $offer
     *
     * @return OrderItem
     * @throws ItemNotFoundException
     */
    public function getItemFromOffer(Offer $offer): OrderItem
    {
        foreach ($this->orderedItems as $orderItem) {
            if ($orderItem->getOrderedItem()->getId()->equals($offer->getId())) {
                return $orderItem;
            }
        }
        throw new ItemNotFoundException('Product offer not found in this order: '.$offer->getItemOffered()->getName());
    }

    /**
     * @param OrderItem[] $items
     *
     * @return Order
     */
    public function addOrderItems(iterable $items): self
    {
        foreach ($items as $item) {
            $this->addItem($item->getOrderedItem(), $item->getOrderQuantity());
        }

        return $this;
    }
}
