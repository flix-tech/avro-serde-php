<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;
use FlixTech\AvroSerializer\Objects\Schema\Generation\VariadicAttribute;

/**
 * @Annotation
 */
final class AvroSymbols implements VariadicAttribute
{
    /**
     * @var array<string>
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::SYMBOLS;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string>
     */
    public function value(): array
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
