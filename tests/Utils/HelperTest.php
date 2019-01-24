<?php

namespace App\Tests;

use App\Module\Product\Feature;
use App\Module\Product\Product;
use App\Module\UuidIdentifiable;
use App\Tests\Utils\Resource\Entity;
use App\Utils\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    /**
     * @dataProvider getUuidEntities
     *
     * @param UuidIdentifiable[] $entities
     */
    public function testMapEntitiesById(array $entities): void
    {
        $mappedByIds = [];
        foreach ($entities as $entity) {
            $mappedByIds[$entity->getId()->toString()] = $entity;
        }

        $result = Helper::mapEntitiesById($entities);

        $this->assertEquals($mappedByIds, $result);
    }

    /**
     * @dataProvider getEntities
     *
     * @param Entity[] $entities
     */
    public function testMapObjectsFromUserKey(array $entities): void
    {
        $mappedByIds = [];
        foreach ($entities as $entity) {
            $mappedByIds[$entity->getName()] = $entity;
        }

        $result = Helper::mapObjectsFromUserKey($entities, function (Entity $object) {
            return $object->getName();
        });

        $this->assertEquals($mappedByIds, $result);
    }


    public function getUuidEntities()
    {
        yield [[]];
        yield [[new Product('foo')]];
        yield [[new Product('bar1'), new Feature('bar2')]];
    }

    public function getEntities()
    {
        $foo = new Entity('foo');
        $bar = new Entity('bar');

        yield [[]];
        yield [[$foo]];
        yield [[$foo, $bar]];
    }
}
