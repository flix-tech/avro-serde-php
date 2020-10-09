<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

/**
 * @Annotation
 */
final class AvroSize implements SchemaAttribute
{
    /**
     * @var int
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::SIZE;
    }

    /**
     * {@inheritdoc}
     */
    public function value(): int
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
