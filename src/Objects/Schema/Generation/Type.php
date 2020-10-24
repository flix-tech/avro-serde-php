<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

class Type
{
    /**
     * @var string
     */
    private $typeName;

    /**
     * @var SchemaAttributes
     */
    private $attributes;

    public function __construct(string $typeName, SchemaAttributes $attributes = null)
    {
        $this->typeName = $typeName;
        $this->attributes = $attributes ?? new SchemaAttributes();
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function getAttributes(): SchemaAttributes
    {
        return $this->attributes;
    }
}
