<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;
use FlixTech\AvroSerializer\Objects\Schema\Generation\TypeOnlyAttribute;

#[\Attribute]
final class AvroValues implements TypeOnlyAttribute
{
    public array $types;

    public function __construct(
        Type|AvroType ...$types,
    ) {
        $this->types = array_map(function ($type) {
            if ($type instanceof AvroType) {
                return $type;
            }

            return new AvroType($type);
        }, $types);
    }

    public function value(): array
    {
        return $this->types;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes(...$this->value());
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::VALUES;
    }
}
