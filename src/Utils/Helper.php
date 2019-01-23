<?php

namespace App\Utils;

use App\Module\UuidIdentifiable;

class Helper
{
    /**
     * @param UuidIdentifiable[] $entities indexed array
     *
     * @return UuidIdentifiable[] mapped array by Uuids
     */
    public static function mapEntitiesById(iterable $entities): array
    {
        return self::mapObjectsFromUserKey($entities, function(UuidIdentifiable $entity) {
            return $entity->getId()->toString();
        });
    }

    /**
     * @param \stdClass[] $objects
     * @param callable    $key
     *
     * @return array
     */
    public static function mapObjectsFromUserKey(iterable $objects, callable $key): array
    {
        $mappedObjects = [];

        foreach ($objects as $object) {
            $mappedObjects[$key($object)] = $object;
        }

        return $mappedObjects;
    }
}
