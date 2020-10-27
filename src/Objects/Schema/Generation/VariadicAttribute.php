<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

/**
 * Marker interface to indicate that the attribute contains variadic values
 */
interface VariadicAttribute extends SchemaAttribute
{
    /**
     * @return array<mixed>
     */
    public function value(): array;
}
