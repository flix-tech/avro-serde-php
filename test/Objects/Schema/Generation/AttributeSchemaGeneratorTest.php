<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema\Generation\AttributeReader;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;

class AttributeSchemaGeneratorTest extends SchemaGeneratorTest
{
    protected function makeSchemaAttributeReader(): SchemaAttributeReader
    {
        return new AttributeReader();
    }
}
