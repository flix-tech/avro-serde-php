<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;
use FlixTech\AvroSerializer\Objects\Schema\Generation\VariadicAttribute;

#[\Attribute]
final class AvroSymbols implements VariadicAttribute
{
    /**
     * @var array<string>
     */
    private array $symbols;

    public function __construct(
        string ...$symbols,
    ) {
        $this->symbols = $symbols;
    }

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
        return $this->symbols;
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
