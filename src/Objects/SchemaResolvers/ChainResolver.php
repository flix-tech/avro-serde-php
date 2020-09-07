<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\SchemaResolvers;

use AvroSchema;
use FlixTech\AvroSerializer\Objects\SchemaResolverInterface;

class ChainResolver implements SchemaResolverInterface
{
    /**
     * @var SchemaResolverInterface[]
     */
    private $chain;

    public function __construct(SchemaResolverInterface ...$chain)
    {
        $this->chain = $chain;
    }

    public function valueSchemaFor($record): AvroSchema
    {
        foreach ($this->chain as $schemaResolver) {
            try {
                return $schemaResolver->valueSchemaFor($record);
            } catch (\Exception $exception) {
                // noop
            }
        }

        throw new \InvalidArgumentException('No schema resolver in the chain is able to resolve the schema for the record');
    }

    public function keySchemaFor($record): ?AvroSchema
    {
        $keySchema = null;

        foreach ($this->chain as $schemaResolver) {
            try {
                if ($keySchema = $schemaResolver->keySchemaFor($record)) {
                    return $keySchema;
                }
            } catch (\Exception $exception) {
                // noop
            }
        }

        return $keySchema;
    }
}
