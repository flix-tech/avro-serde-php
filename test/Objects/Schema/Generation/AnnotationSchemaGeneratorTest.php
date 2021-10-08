<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AnnotationReader as SchemaAnnotationReader;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;

class AnnotationSchemaGeneratorTest extends SchemaGeneratorTest
{
    protected function makeSchemaAttributeReader(): SchemaAttributeReader
    {
        AnnotationRegistry::registerLoader('class_exists');

        return new SchemaAnnotationReader(
            new AnnotationReader()
        );
    }

}
