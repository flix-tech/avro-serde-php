<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use ReflectionClass;
use ReflectionProperty;

interface SchemaAttributeReader
{
    /**
     * @param ReflectionClass<object> $class
     */
    public function readClassAttributes(ReflectionClass $class): SchemaAttributes;

    public function readPropertyAttributes(ReflectionProperty $property): SchemaAttributes;
}
