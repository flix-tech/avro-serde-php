<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

class FieldOrder extends FieldOption
{
    public function __construct(string $order)
    {
        parent::__construct('order', $order);
    }

    public static function asc(): self
    {
        return new self('ascending');
    }

    public static function desc(): self
    {
        return new self('descending');
    }

    public static function ignore(): self
    {
        return new self('ignore');
    }
}
