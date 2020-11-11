<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\Record\FieldOrder;
use FlixTech\AvroSerializer\Objects\Schema\TypeName;
use ReflectionClass;
use ReflectionProperty;

class SchemaGenerator
{
    /**
     * @var SchemaAttributeReader
     */
    private $reader;

    /**
     * @var TypeMapper
     */
    private $typeMapper;

    public function __construct(SchemaAttributeReader $reader)
    {
        $this->reader = $reader;
        $this->typeMapper = new TypeMapper($this);
    }

    /**
     * @param class-string<object> $className
     */
    public function generate(string $className): Schema
    {
        $class = new ReflectionClass($className);
        $attributes = $this->reader->readClassAttributes($class);

        return $this->generateFromClass($class, new Type(TypeName::RECORD, $attributes));
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function generateFromClass(ReflectionClass $class, Type $type): Schema
    {
        $schema = $this->schemaFromType($type);

        if (!$schema instanceof Schema\RecordType) {
            return $schema;
        }

        foreach ($class->getProperties() as $property) {
            /** @var Schema\RecordType $schema */
            $schema = $this->parseField($property, $schema);
        }

        return $schema;
    }

    private function schemaFromTypes(Type ...$types): Schema
    {
        if (\count($types) > 1) {
            $unionSchemas = \array_map([$this, 'schemaFromType'], $types);

            return Schema::union(...$unionSchemas);
        }

        return $this->schemaFromType($types[0]);
    }

    private function schemaFromType(Type $type): Schema
    {
        return $this->applyAttributes(
            $this->typeMapper->toSchema($type),
            $type->getAttributes()
        );
    }

    private function parseField(ReflectionProperty $property, Schema\RecordType $rootSchema): Schema
    {
        $attributes = $this->reader->readPropertyAttributes($property);

        if (0 === \count($attributes)) {
            return $rootSchema;
        }

        $fieldSchema = $this->schemaFromTypes(...$attributes->types());

        $fieldArgs = [
            $attributes->has(AttributeName::NAME) ? $attributes->get(AttributeName::NAME) : $property->getName(),
            $fieldSchema,
        ];

        if ($attributes->has(AttributeName::DOC)) {
            $fieldArgs[] = Schema\Record\FieldOption::doc($attributes->get(AttributeName::DOC));
        }

        if ($attributes->has(AttributeName::DEFAULT)) {
            $fieldArgs[] = Schema\Record\FieldOption::default($attributes->get(AttributeName::DEFAULT));
        }

        if ($attributes->has(AttributeName::ORDER)) {
            $fieldArgs[] = new FieldOrder($attributes->get(AttributeName::ORDER));
        }

        if ($attributes->has(AttributeName::ALIASES)) {
            $fieldArgs[] = Schema\Record\FieldOption::aliases(
                ...$attributes->get(AttributeName::ALIASES)
            );
        }

        return $rootSchema
            ->field(...$fieldArgs);
    }

    private function applyAttributes(Schema $schema, SchemaAttributes $attributes): Schema
    {
        foreach ($attributes->options() as $attribute) {
            if ($attribute instanceof VariadicAttribute) {
                $schema = $schema->{$attribute->name()}(...$attribute->value());

                continue;
            }

            if ($attribute instanceof TypeOnlyAttribute) {
                $types = $attribute->attributes()->types();
                $schema = $schema->{$attribute->name()}($this->schemaFromTypes(...$types));

                continue;
            }

            if (empty($attribute->name()) || AttributeName::TARGET_CLASS === $attribute->name()) {
                continue;
            }

            $schema = $schema->{$attribute->name()}($attribute->value());
        }

        return $schema;
    }
}
