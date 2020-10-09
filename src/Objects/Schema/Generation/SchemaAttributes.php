<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;

class SchemaAttributes implements \Countable
{
    /**
     * @var array<string, array<SchemaAttribute>>
     */
    private $attributes = [];

    /**
     * @var array<SchemaAttribute>
     */
    private $optionAttributes = [];

    public function __construct(SchemaAttribute ...$attributes)
    {
        foreach ($attributes as $attribute) {
            $this->add($attribute);
        }
    }

    /**
     * @return array<SchemaAttribute>
     */
    public function types(): array
    {
        return $this->attributes[AttributeName::TYPE] ?? [];
    }

    /**
     * @return array<SchemaAttribute>
     */
    public function options(): array
    {
        return $this->optionAttributes;
    }

    public function has(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->attributes[$name][0]->value();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return \count($this->attributes);
    }

    private function add(SchemaAttribute $attribute): void
    {
        $this->attributes[$attribute->name()][] = $attribute;

        if (AttributeName::TYPE !== $attribute->name()) {
            $this->optionAttributes[] = $attribute;
        }
    }
}
