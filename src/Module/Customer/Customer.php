<?php

declare(strict_types=1);

namespace App\Module\Customer;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Module\Order\Order;
use App\Module\UuidIdentifiable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An account for a person who place orders.
 *
 * @ORM\Entity
 * @ORM\Table(name="customer")
 *
 * //@ApiResource
 */
class Customer implements UuidIdentifiable, UserInterface
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $login;

    /**
     * @var Order[]|Collection<Order>
     *
     * @ORM\OneToMany(targetEntity="App\Module\Order\Order", mappedBy="customer")
     */
    private $orders;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=72)
     * @Assert\Type(type="string")
     * @Assert\NotNull
     */
    private $password;

    /**
     * @var Person
     *
     * @ORM\OneToOne(targetEntity="Person")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull
     */
    private $person;

    /**
     * @var string[]
     */
    private $roles = ['ROLE_CUSTOMER'];

    /**
     * @var Collection<Order>
     *
     * @ORM\ManyToMany(targetEntity="App\Module\Order\Order")
     * @ORM\JoinTable(name="shopping_cart",
     *      joinColumns={@JoinColumn(unique=true)},
     *      inverseJoinColumns={@JoinColumn(unique=true)}
     * )
     */
    private $shoppingCart;

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


    public function __construct(string $login, string $password, Person $person)
    {
        $this->id = Uuid::uuid4();
        $this->login = $login;
        $this->password = $password;
        $this->person = $person;
        $this->orders = new ArrayCollection();
        $this->shoppingCart = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getShoppingCart(): ?Order
    {
        return $this->shoppingCart->first() ?: null;
    }

    public function createShoppingCart(): Order
    {
        return $this->shoppingCart[] = Order::fromCustomer($this);
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
