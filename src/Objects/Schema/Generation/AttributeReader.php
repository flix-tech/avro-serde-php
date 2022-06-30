<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Exceptions\Exceptions;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;

class AttributeReader implements SchemaAttributeReader
{
    private const MINIMUM_REQUIRED_VERSION = '8.1';

    public function __construct()
    {
        if (version_compare(PHP_VERSION, self::MINIMUM_REQUIRED_VERSION) < 0) {
            throw Exceptions::forPhpVersion(
                PHP_VERSION,
                self::MINIMUM_REQUIRED_VERSION,
            );
        }
    }

    public function readClassAttributes(ReflectionClass $class): SchemaAttributes
    {
        $attributes = $class->getAttributes();
        return $this->getSchemaAttributes(...$attributes);
    }

    public function readPropertyAttributes(ReflectionProperty $property): SchemaAttributes
    {
        $attributes = $property->getAttributes();
        return $this->getSchemaAttributes(...$attributes);
    }

    private function getSchemaAttributes(ReflectionAttribute ...$attributes): SchemaAttributes
    {
        $attributes = array_map(fn($attr) => $attr->newInstance(), $attributes);
        $attributes = array_filter($attributes, fn($attr) => $attr instanceof SchemaAttribute);
        return new SchemaAttributes(...$attributes);
    }
}
