<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class PrimitiveTypeTest extends TestCase
{
    /**
     * @dataProvider providePrimitiveTypes()
     * @test
     */
    public function it_should_serialize_primitive_types(Schema $type, string $expectedName)
    {
        $this->assertEquals($expectedName, $type->serialize());
    }

    /**
     * @dataProvider providePrimitiveTypes()
     * @test
     */
    public function it_should_parse_primitive_types(Schema $type, string $expectedName)
    {
        $parsedSchema = $type->parse();
        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
        $this->assertEquals($expectedName, $parsedSchema->type());
    }

    public function providePrimitiveTypes(): array
    {
        return [
            'null' => [Schema::null(), 'null'],
            'boolean' => [Schema::boolean(), 'boolean'],
            'int' => [Schema::int(), 'int'],
            'long' => [Schema::long(), 'long'],
            'float' => [Schema::float(), 'float'],
            'double' => [Schema::double(), 'double'],
            'bytes' => [Schema::bytes(), 'bytes'],
            'string' => [Schema::string(), 'string'],
        ];
    }
}
