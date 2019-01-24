<?php

declare(strict_types=1);

namespace App\Tests\Utils\Resource;

class Entity
{
    /** @var int $id */
    private $id;

    /** @var string $name */
    private $name;


    public function __construct(string $name)
    {
        $this->id = \mt_rand(1, 10);
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
