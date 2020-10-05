<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

use FlixTech\AvroSerializer\Objects\Definition;
use FlixTech\AvroSerializer\Objects\Schema;

class Field implements Definition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Schema
     */
    private $type;

    /**
     * @var array<FieldOption>
     */
    private $options;

    public function __construct(
        string $name,
        Schema $type,
        FieldOption ...$options
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->options = $options;
    }

    /**
     * @return array<mixed>
     */
    public function serialize(): array
    {
        $field = [
            'name' => $this->name,
            'type' => $this->type->serialize(),
        ];

        foreach ($this->options as $option) {
            $field[$option->getName()] = $option->getValue();
        }

        return $field;
    }
}
