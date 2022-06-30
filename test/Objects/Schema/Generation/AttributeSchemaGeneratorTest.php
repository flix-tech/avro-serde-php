<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema\Generation\AttributeReader;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\ArraysWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\EmptyRecord;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\MapsWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\PrimitiveTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\RecordWithComplexTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Attributes\RecordWithRecordType;

/**
 * @requires PHP >= 8.1
 */
class AttributeSchemaGeneratorTest extends SchemaGeneratorTest
{
    protected function makeSchemaAttributeReader(): SchemaAttributeReader
    {
        return new AttributeReader();
    }

    protected function getEmptyRecordClass(): string
    {
        return EmptyRecord::class;
    }

    protected function getPrimitiveTypesClass(): string
    {
        return PrimitiveTypes::class;
    }

    protected function getRecordWithComplexTypesClass(): string
    {
        return RecordWithComplexTypes::class;
    }

    protected function getRecordWithRecordTypeClass(): string
    {
        return RecordWithRecordType::class;
    }

    protected function getArraysWithComplexTypeClass(): string
    {
        return ArraysWithComplexType::class;
    }

    protected function getMapsWithComplexTypeClass(): string
    {
        return MapsWithComplexType::class;
    }
}
