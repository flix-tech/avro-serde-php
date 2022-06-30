<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use Attribute;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[Attribute]
class AvroNamespace implements SchemaAttribute
{
    public function __construct(
        private string $value,
    ) {
    }

    public function name(): string
    {
        return AttributeName::NAMESPACE;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
