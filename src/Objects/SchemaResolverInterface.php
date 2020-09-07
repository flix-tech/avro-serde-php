<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use AvroSchema;

/**
 * Resolves value and/or key schemas for a given record.
 */
interface SchemaResolverInterface
{
    /**
     * @param mixed $record
     */
    public function valueSchemaFor($record): AvroSchema;

    /**
     * This method should resolve the Avro key schema for a given record.
     *
     * The method should return `NULL` *only* when the record is not supposed to have a key schema.
     * If the key schema cannot be resolved otherwise, this method should throw an `CannotResolveSchemaException`.
     *
     * @param mixed $record
     */
    public function keySchemaFor($record): ?AvroSchema;
}
