<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

class FieldDoc extends FieldOption
{
    public function __construct(string $doc)
    {
        parent::__construct('doc', $doc);
    }
}
