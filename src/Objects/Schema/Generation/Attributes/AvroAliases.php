<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;
use FlixTech\AvroSerializer\Objects\Schema\Generation\VariadicAttribute;

#[\Attribute]
final class AvroAliases implements VariadicAttribute
{
    /**
     * @var array<string>
     */
    public array $aliases;

    public function __construct(
        string ...$aliases,
    ) {
        $this->aliases = $aliases;
    }

    public function name(): string
    {
        return AttributeName::ALIASES;
    }

    public function value(): array
    {
        return $this->aliases;
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes();
    }
}
