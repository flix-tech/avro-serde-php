<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AnnotationReader as SchemaAnnotationReader;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\ArraysWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\EmptyRecord;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\MapsWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\PrimitiveTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\RecordWithComplexTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\Annotations\RecordWithRecordType;

class AnnotationSchemaGeneratorTest extends SchemaGeneratorTest
{
    protected function makeSchemaAttributeReader(): SchemaAttributeReader
    {
        AnnotationRegistry::registerLoader('class_exists');

        return new SchemaAnnotationReader(
            new AnnotationReader()
        );
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
