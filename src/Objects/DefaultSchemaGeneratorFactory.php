<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaGenerator;

class DefaultSchemaGeneratorFactory
{
    public static function get(): SchemaGenerator
    {
        AnnotationRegistry::registerLoader('class_exists');

        return new Schema\Generation\SchemaGenerator(
            new Schema\Generation\AnnotationReader(
                new AnnotationReader()
            )
        );
    }
}
