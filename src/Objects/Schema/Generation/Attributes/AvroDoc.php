<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;


#[\Attribute]
final class AvroDoc implements SchemaAttribute
{
    public function __construct(
        public string $value
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::DOC;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): string
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
