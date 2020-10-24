<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttribute;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;

trait ContainsOnlyTypes
{
    /**
     * @phpstan-var string|AvroType|array<string|AvroType>
     */
    public $value;

    /**
     * {@inheritdoc}
     *
     * @return array<SchemaAttribute>
     */
    public function value(): array
    {
        if (!\is_array($this->value)) {
            return [$this->valueToType($this->value)];
        }

        return \array_map([$this, 'valueToType'], $this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes(...$this->value());
    }

    /**
     * @param string|AvroType $value
     */
    private function valueToType($value): AvroType
    {
        if ($value instanceof AvroType) {
            return $value;
        }

        return AvroType::create($value);
    }
}
