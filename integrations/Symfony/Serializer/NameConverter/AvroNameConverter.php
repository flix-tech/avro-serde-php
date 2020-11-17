<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Integrations\Symfony\Serializer\NameConverter;

use Exception;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaAttributeReader;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

if (!\interface_exists(AdvancedNameConverterInterface::class)) {
    throw new Exception("The advanced name converter is supported only in symfony 4 and forward");
}

class AvroNameConverter implements AdvancedNameConverterInterface
{
    /**
     * @var SchemaAttributeReader
     */
    private $attributeReader;

    /**
     * @var array<string, PropertyNameMap>
     */
    private $mapCache = [];

    public function __construct(SchemaAttributeReader $attributeReader)
    {
        $this->attributeReader = $attributeReader;
    }

    public function normalize(
        $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        return $this
            ->getNameMap($class, $format)
            ->getNormalized($propertyName);
    }

    private function getNameMap(?string $class, ?string $format): PropertyNameMap
    {
        if (null === $class || !class_exists($class)) {
            return new PropertyNameMap();
        }

        if (null === $format || AvroSerDeEncoder::FORMAT_AVRO !== $format) {
            return new PropertyNameMap();
        }

        return $this->generateMap($class);
    }

    private function generateMap(string $class): PropertyNameMap
    {
        if (isset($this->mapCache[$class])) {
            return $this->mapCache[$class];
        }

        $reflectionClass = new ReflectionClass($class);

        $map = array_reduce(
            $reflectionClass->getProperties(),
            [$this, 'propertyToSchemaName'],
            new PropertyNameMap()
        );

        $this->mapCache[$class] = $map;

        return $map;
    }

    private function propertyToSchemaName(
        PropertyNameMap $map,
        ReflectionProperty $reflectionProperty
    ): PropertyNameMap {
        $schemaAttributes = $this->attributeReader->readPropertyAttributes($reflectionProperty);

        if (!$schemaAttributes->has(AttributeName::NAME)) {
            return $map;
        }

        $attributeName = $schemaAttributes->get(AttributeName::NAME);

        if (!is_string($attributeName)) {
            return $map;
        }

        return $map->add(
            $reflectionProperty->getName(),
            $attributeName
        );
    }

    public function denormalize(
        $propertyName,
        string $class = null,
        string $format = null,
        array $context = []
    ): string {
        return $this
            ->getNameMap($class, $format)
            ->getDenormalized($propertyName);
    }
}
