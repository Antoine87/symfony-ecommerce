<?php

declare(strict_types=1);

namespace App\Module;

use Ramsey\Uuid\UuidInterface;

interface UuidIdentifiable
{
    public function getId(): UuidInterface;
}
