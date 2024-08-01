<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[\Attribute]
final class AvroSize implements SchemaAttribute
{
    public function __construct(
        public int $size,
    ) {
    }

    public function name(): string
    {
        return AttributeName::SIZE;
    }

    public function value(): int
    {
        return $this->size;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
