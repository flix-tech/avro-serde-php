<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;

abstract class PrimitiveType extends Schema
{
    /**
     * @var string
     */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    final public function serialize(): string
    {
        return $this->type;
    }
}
