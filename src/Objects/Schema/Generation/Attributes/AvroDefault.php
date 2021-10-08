<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[\Attribute]
final class AvroDefault implements SchemaAttribute
{
    public function __construct(
        public mixed $value,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::DEFAULT;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
