<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_ALL)]
class AvroType implements SchemaAttribute
{
    /**
     * @var array<SchemaAttribute>
     */
    public array $attributes = [];

    public string $value;

    public function __construct(
        Type|string $value,
        SchemaAttribute ...$attributes
    ) {
        $this->value = \is_string($value) ? $value : $value->value;

        $this->attributes = $attributes;
    }

    public function name(): string
    {
        return AttributeName::TYPE;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes(...$this->attributes);
    }
}
