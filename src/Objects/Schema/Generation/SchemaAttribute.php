<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

interface SchemaAttribute
{
    public function name(): string;

    /**
     * @return mixed
     */
    public function value();

    public function attributes(): SchemaAttributes;
}
