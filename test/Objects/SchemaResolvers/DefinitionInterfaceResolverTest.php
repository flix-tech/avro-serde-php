<?php

namespace FlixTech\AvroSerializer\Test\Objects\SchemaResolvers;

use FlixTech\AvroSerializer\Objects\HasSchemaDefinitionInterface;
use FlixTech\AvroSerializer\Objects\SchemaResolvers\DefinitionInterfaceResolver;
use PHPUnit\Framework\TestCase;

class DefinitionInterfaceResolverTest extends TestCase
{
    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_allow_correct_interfaces(): void
    {
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();
        $definitionClass = $this->createAnonymousDefinitionInterface(
            '{"type": "string"}'
        );

        $this->assertEquals(
            \AvroSchema::parse('{"type": "string"}'),
            $definitionInterfaceResolver->valueSchemaFor($definitionClass)
        );

        $this->assertNull($definitionInterfaceResolver->keySchemaFor($definitionClass));

        $definitionClass = $this->createAnonymousDefinitionInterface(
            '{"type": "string"}',
            '{"type": "int"}'
        );

        $this->assertEquals(
            \AvroSchema::parse('{"type": "string"}'),
            $definitionInterfaceResolver->valueSchemaFor($definitionClass)
        );

        $this->assertEquals(
            \AvroSchema::parse('{"type": "int"}'),
            $definitionInterfaceResolver->keySchemaFor($definitionClass)
        );
    }

    /**
     * @test
     */
    public function it_should_fail_for_records_not_implementing_the_interface_for_value_schema(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();

        $definitionInterfaceResolver->valueSchemaFor([]);
    }

    /**
     * @test
     */
    public function it_should_fail_for_records_not_implementing_the_interface_for_key_schema(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $definitionInterfaceResolver = new DefinitionInterfaceResolver();

        $definitionInterfaceResolver->keySchemaFor([]);
    }

    private function createAnonymousDefinitionInterface(string $valueSchema, string $keySchema = null): HasSchemaDefinitionInterface
    {
        $class = new class() implements HasSchemaDefinitionInterface {
            /**
             * @var string
             */
            public static $valueSchema;

            /**
             * @var string|null
             */
            public static $keySchema;

            public static function valueSchemaJson(): string
            {
                return self::$valueSchema;
            }

            public static function keySchemaJson(): ?string
            {
                return self::$keySchema;
            }
        };

        $class::$valueSchema = $valueSchema;
        $class::$keySchema = $keySchema;

        return $class;
    }
}
