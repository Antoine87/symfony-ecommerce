<?php

declare(strict_types=1);

namespace App\Module;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;

/**
 * Callable class for an entity query (Isolated method of a repository)
 *
 * @see http://php.net/manual/language.types.callable.php
 * @see http://php.net/manual/language.oop5.magic.php#object.invoke
 */
interface QueryFunction extends ServiceEntityRepositoryInterface
{
    /* public function __invoke(...); */
}
