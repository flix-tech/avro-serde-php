<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\SchemaResolvers;

use Assert\Assert;
use AvroSchema;
use FlixTech\AvroSerializer\Objects\HasSchemaDefinitionInterface;
use FlixTech\AvroSerializer\Objects\SchemaResolverInterface;

class DefinitionInterfaceResolver implements SchemaResolverInterface
{
    /**
     * @param $record
     *
     * @return \AvroSchema
     *
     * @throws \AvroSchemaParseException
     */
    public function valueSchemaFor($record): AvroSchema
    {
        /** @var HasSchemaDefinitionInterface $record */
        $this->guardRecordHasDefinition($record);

        return AvroSchema::parse($record::valueSchemaJson());
    }

    /**
     * @param mixed $record
     *
     * @return \AvroSchema|null
     *
     * @throws \AvroSchemaParseException
     */
    public function keySchemaFor($record): ?AvroSchema
    {
        $this->guardRecordHasDefinition($record);

        $keySchemaJson = $record::keySchemaJson();

        if (!$keySchemaJson) {
            return null;
        }

        return AvroSchema::parse($keySchemaJson);
    }

    /**
     * @param HasSchemaDefinitionInterface $record
     */
    private function guardRecordHasDefinition($record)
    {
        Assert::that($record)
            ->isObject()
            ->implementsInterface(HasSchemaDefinitionInterface::class);
    }
}
