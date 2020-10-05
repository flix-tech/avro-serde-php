<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;

class ArrayType extends ComplexType
{
    public function __construct()
    {
        parent::__construct('array');
    }

    public function items(Schema $schema): self
    {
        return $this->attribute('items', $schema);
    }

    /**
     * @param array<mixed> $default
     */
    public function default(array $default): self
    {
        return $this->attribute('default', $default);
    }
}
