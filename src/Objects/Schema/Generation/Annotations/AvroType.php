<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

/**
 * @Annotation
 */
final class AvroType implements SchemaAttribute
{
    /**
     * @var string
     */
    public $value;

    /**
     * @var array<\FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute>
     */
    public $attributes = [];

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
