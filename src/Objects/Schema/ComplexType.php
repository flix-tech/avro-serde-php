<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Definition;
use FlixTech\AvroSerializer\Objects\Schema;

abstract class ComplexType extends Schema
{
    /**
     * @var array<string, mixed>
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $value
     *
     * @return static
     */
    public function attribute(string $name, $value): self
    {
        $schema = clone $this;
        $schema->attributes[$name] = $value;

        return $schema;
    }

    /**
     * @return array<mixed>
     */
    public function serialize(): array
    {
        $record = [
            'type' => $this->type,
        ];

        foreach ($this->attributes as $attributeName => $attributeValue) {
            if ($attributeValue instanceof Definition) {
                $record[$attributeName] = $attributeValue->serialize();

                continue;
            }

            $record[$attributeName] = $attributeValue;
        }

        return $record;
    }
}
