<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Annotations;

use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributes;
use FlixTech\AvroSerializer\Objects\Schema\Generation\TypeOnlyAttribute;

/**
 * @Annotation
 */
final class AvroValues implements TypeOnlyAttribute
{
    /**
     * @var mixed
     */
    public $value;

    public function value(): array
    {
        $value = \is_array($this->value) ? $this->value : [$this->value];

        return array_map(function ($value) {
            if ($value instanceof AvroType) {
                return $value;
            }

            return AvroType::create($value);
        }, $value);
    }

    public function attributes(): SchemaAttributes
    {
        return new SchemaAttributes(...$this->value());
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return AttributeName::VALUES;
    }
}
