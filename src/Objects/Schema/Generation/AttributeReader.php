<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

class AttributeReader implements SchemaAttributeReader
{
    public function readClassAttributes(\ReflectionClass $class): SchemaAttributes
    {
        $attributes = $class->getAttributes();

        return $this->getSchemaAttributes(...$attributes);
    }

    public function readPropertyAttributes(\ReflectionProperty $property): SchemaAttributes
    {
        $attributes = $property->getAttributes();

        return $this->getSchemaAttributes(...$attributes);
    }

    private function getSchemaAttributes(\ReflectionAttribute ...$attributes): SchemaAttributes
    {
        $attributes = array_map(fn ($attr) => $attr->newInstance(), $attributes);
        $attributes = array_filter($attributes, fn ($attr) => $attr instanceof SchemaAttribute);

        return new SchemaAttributes(...$attributes);
    }
}
