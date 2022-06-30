<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[\Attribute]
final class AvroOrder implements SchemaAttribute
{
    public function __construct(
        private Order $order,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::ORDER;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): string
    {
        return $this->order->value;
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
