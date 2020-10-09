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

    public function __construct(SchemaAttributeReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param class-string<object> $className
     */
    public function generate(string $className): Schema
    {
        $class = new ReflectionClass($className);
        $attributes = $this->reader->readClassAttributes($class);

        return $this->generateFromClass($class, $attributes);
    }

    /**
     * @param ReflectionClass<object> $class
     */
    private function generateFromClass(ReflectionClass $class, SchemaAttributes $attributes): Schema
    {
        $type = $attributes->types()[0];
        $schema = $this->schemaFromType($type->value(), $attributes);

        if (!$schema instanceof Schema\RecordType) {
            return $schema;
        }

        foreach ($class->getProperties() as $property) {
            /** @var Schema\RecordType $schema */
            $schema = $this->parseField($property, $schema);
        }

        return $schema;
    }

    private function schemaFromType(string $type, SchemaAttributes $attributes): Schema
    {
        switch ($type) {
            case TypeName::RECORD:
                if ($attributes->has(AttributeName::TARGET_CLASS)) {
                    return $this->generate($attributes->get(AttributeName::TARGET_CLASS));
                }
                $schema = Schema::record();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::NULL:
                $schema = Schema::null();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::BOOLEAN:
                $schema = Schema::boolean();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::INT:
                $schema = Schema::int();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::LONG:
                $schema = Schema::long();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::FLOAT:
                $schema = Schema::float();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::DOUBLE:
                $schema = Schema::double();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::BYTES:
                $schema = Schema::bytes();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::STRING:
                $schema = Schema::string();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::ARRAY:
                $schema = Schema::array();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::MAP:
                $schema = Schema::map();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::ENUM:
                $schema = Schema::enum();

                return $this->applyAttributes($schema, $attributes);
            case TypeName::FIXED:
                $schema = Schema::fixed();

                return $this->applyAttributes($schema, $attributes);
            default:
                throw new \InvalidArgumentException('$type is not a valid avro type');
        }
    }

    private function parseField(ReflectionProperty $property, Schema\RecordType $rootSchema): Schema
    {
        $attributes = $this->reader->readPropertyAttributes($property);

        if (0 === \count($attributes)) {
            return $rootSchema;
        }

        $mainType = $attributes->types()[0];
        $fieldSchema = $this->schemaFromType($mainType->value(), $mainType->attributes());

        if (1 < \count($attributes->types())) {
            $unionSchemas = \array_map(function (SchemaAttribute $type) {
                return $this->schemaFromType($type->value(), $type->attributes());
            }, $attributes->types());

            $fieldSchema = Schema::union(...$unionSchemas);
        }

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
        $variadicValues = [
            AttributeName::SYMBOLS,
            AttributeName::ALIASES,
        ];

        $schemaValues = [
            AttributeName::ITEMS,
            AttributeName::VALUES,
        ];

        foreach ($attributes->options() as $attribute) {
            if (\in_array($attribute->name(), $variadicValues) && \is_array($attribute->value())) {
                $schema = $schema->{$attribute->name()}(...$attribute->value());

                continue;
            }

            if (\in_array($attribute->name(), $schemaValues) && \is_string($attribute->value())) {
                $schema = $schema->{$attribute->name()}($this->schemaFromType($attribute->value(), new SchemaAttributes()));

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
