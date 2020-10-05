<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

class FieldDefault extends FieldOption
{
    /**
     * @param mixed $default
     */
    public function __construct($default)
    {
        parent::__construct('default', $default);
    }
}
