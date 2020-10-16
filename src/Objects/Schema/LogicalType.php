<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

abstract class LogicalType extends ComplexType
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(string $logicalType, string $annotatedType, array $attributes = [])
    {
        $attributes[AttributeName::LOGICAL_TYPE] = $logicalType;

        parent::__construct($annotatedType, $attributes);
    }
}
