<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;

class LocalTimestampMillisType extends LogicalType
{
    public function __construct()
    {
        parent::__construct('local-timestamp-millis', Schema::long()->serialize());
    }
}
