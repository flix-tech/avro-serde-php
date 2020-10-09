<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class FloatType extends PrimitiveType
{
    public function __construct()
    {
        parent::__construct(TypeName::FLOAT);
    }
}
