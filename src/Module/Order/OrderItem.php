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
 * An order item is a line of an order. It includes the quantity and shipping details of a bought offer.
 *
 * @see http://schema.org/OrderItem
 *
 * @ORM\Entity
 * @ORM\Table(name="order_item")
 *
 * //@ApiResource(iri="http://schema.org/OrderItem")
 */
class OrderItem implements UuidIdentifiable
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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orderedItems")
     */
    private $order;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ApiProperty(iri="http://schema.org/orderQuantity")
     * @Assert\Type(type="integer")
     * @Assert\NotNull
     */
    private $orderQuantity;

    /**
     * @var Offer
     *
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumn(nullable=false)
     * @ApiProperty(iri="http://schema.org/orderedItem")
     */
    private $orderedItem;


    public function __construct(Order $order, Offer $item, int $quantity)
    {
        $this->id = Uuid::uuid4();
        $this->order = $order;
        $this->orderedItem = $item;
        $this->orderQuantity = $quantity;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getOrderedItem(): Offer
    {
        return $this->orderedItem;
    }

    public function getOrderQuantity(): int
    {
        return $this->orderQuantity;
    }

    public function addQuantity(int $quantity): self
    {
        $this->orderQuantity += $quantity;

        return $this;
    }
}
