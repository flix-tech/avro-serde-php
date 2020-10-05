<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;

class UnionType extends Schema
{
    /**
     * @var array<Schema>
     */
    private $types;

    public function __construct(Schema $type, Schema ...$types)
    {
        \array_unshift($types, $type);
        $this->types = $types;
    }

    /**
     * @return array<mixed>
     */
    public function serialize(): array
    {
        return \array_map(function (Schema $schema) {
            return $schema->serialize();
        }, $this->types);
    }
}
