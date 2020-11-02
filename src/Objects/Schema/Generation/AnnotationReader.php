<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use Doctrine\Common\Annotations\Reader as DoctrineAnnotationReader;
use ReflectionClass;
use ReflectionProperty;

class AnnotationReader implements SchemaAttributeReader
{
    /**
     * @var DoctrineAnnotationReader
     */
    private $reader;

    public function __construct(DoctrineAnnotationReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function readClassAttributes(ReflectionClass $class): SchemaAttributes
    {
        /** @var SchemaAttribute[] $annotations */
        $annotations = $this->reader->getClassAnnotations($class);
        $attributes = \array_filter($annotations, [$this, 'onlySchemaAttributes']);

        return new SchemaAttributes(...$attributes);
    }

    public function readPropertyAttributes(ReflectionProperty $property): SchemaAttributes
    {
        /** @var SchemaAttribute[] $annotations */
        $annotations = $this->reader->getPropertyAnnotations($property);
        $attributes = \array_filter($annotations, [$this, 'onlySchemaAttributes']);

        return new SchemaAttributes(...$attributes);
    }

    private function onlySchemaAttributes(object $annotation): bool
    {
        return $annotation instanceof SchemaAttribute;
    }
}
