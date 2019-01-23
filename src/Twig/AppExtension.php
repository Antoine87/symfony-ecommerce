<?php

namespace App\Twig;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;


    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function getFilters(): array
    {
        return [
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('average', [$this, 'average']),
        ];
    }

    public function average(\Traversable $entities, string $property)
    {
        $values = [];

        foreach ($entities as $entity) {
            $values[] = $this->propertyAccessor->getValue($entity, $property);
        }

        return $values
            ? array_sum($values) / count($values)
            : 0;
    }
}
