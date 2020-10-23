<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Integrations\Symfony\Serializer\NameConverter;

class PropertyNameMap
{
    private $propertyToSchemaMap = [];
    private $schemaToPropertyMap = [];

    public function add(string $propertyName, string $schemaName): self
    {
        $map = clone $this;

        $map->propertyToSchemaMap[$propertyName] = $schemaName;
        $map->schemaToPropertyMap[$schemaName] = $propertyName;

        return $map;
    }

    public function getNormalized(string $name): string
    {
        return $this->propertyToSchemaMap[$name] ?? $name;
    }

    public function getDenormalized(string $name): string
    {
        return $this->schemaToPropertyMap[$name] ?? $name;
    }
}
