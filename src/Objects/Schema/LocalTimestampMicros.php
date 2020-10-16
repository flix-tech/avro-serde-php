<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class LocalTimestampMicros extends LogicalType
{
    public function __construct()
    {
        parent::__construct(TypeName::LOCAL_TIMESTAMP_MICROS, TypeName::LONG);
    }
}
