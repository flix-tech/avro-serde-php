<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

interface Definition
{
    /**
     * @return mixed
     */
    public function serialize();
}
