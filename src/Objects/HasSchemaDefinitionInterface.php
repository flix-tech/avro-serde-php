<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

interface HasSchemaDefinitionInterface
{
    public static function valueSchemaJson(): string;

    public static function keySchemaJson(): ?string;
}
