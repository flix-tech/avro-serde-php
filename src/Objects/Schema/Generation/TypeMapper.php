<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema;
use FlixTech\AvroSerializer\Objects\Schema\AttributeName;
use FlixTech\AvroSerializer\Objects\Schema\TypeName;

final class TypeMapper
{
    /**
     * @var array<string, callable>
     */
    private $mappers;

    public function __construct(SchemaGenerator $generator)
    {
        $this->mappers = [
            TypeName::RECORD => $this->recordType($generator),
            TypeName::NULL => $this->simpleType(Schema::null()),
            TypeName::BOOLEAN => $this->simpleType(Schema::boolean()),
            TypeName::INT => $this->simpleType(Schema::int()),
            TypeName::LONG => $this->simpleType(Schema::long()),
            TypeName::FLOAT => $this->simpleType(Schema::float()),
            TypeName::DOUBLE => $this->simpleType(Schema::double()),
            TypeName::BYTES => $this->simpleType(Schema::bytes()),
            TypeName::STRING => $this->simpleType(Schema::string()),
            TypeName::ARRAY => $this->simpleType(Schema::array()),
            TypeName::MAP => $this->simpleType(Schema::map()),
            TypeName::ENUM => $this->simpleType(Schema::enum()),
            TypeName::FIXED => $this->simpleType(Schema::fixed()),
        ];
    }

    public function toSchema(Type $type): Schema
    {
        $mapper = $this->mappers[$type->getTypeName()] ?? $this->namedType();

        return $mapper($type);
    }

    private function simpleType(Schema $schema): callable
    {
        return function () use ($schema): Schema {
            return $schema;
        };
    }

    private function recordType(SchemaGenerator $generator): callable
    {
        return function (Type $type) use ($generator): Schema {
            $attributes = $type->getAttributes();

            if ($attributes->has(AttributeName::TARGET_CLASS)) {
                return $generator->generate($attributes->get(AttributeName::TARGET_CLASS));
            }

            return Schema::record();
        };
    }

    private function namedType(): callable
    {
        return function (Type $type): Schema {
            return Schema::named($type->getTypeName());
        };
    }
}
