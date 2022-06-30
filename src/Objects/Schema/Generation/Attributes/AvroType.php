<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use Attribute;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[Attribute(Attribute::IS_REPEATABLE|Attribute::TARGET_ALL)]
final class AvroType implements SchemaAttribute
{
    /**
     * @var array<SchemaAttribute>
     */
    public $attributes = [];

    public string $value;

    public function __construct(
        Type|string $value,
        SchemaAttribute ...$attributes
    ) {
        if (is_string($value)) {
            $this->value = $value;
        } else {
            $this->value = $value->value;
        }

        $this->attributes = $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::TYPE;
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
        return new SchemaAttributes(...$this->attributes);
    }
}
