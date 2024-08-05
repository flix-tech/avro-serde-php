<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class PrimitiveTypeTest extends TestCase
{
    #[DataProvider('providePrimitiveTypes')]
    #[Test]
    public function it_should_serialize_primitive_types(Schema $type, string $expectedName): void
    {
        $this->assertEquals($expectedName, $type->serialize());
    }

    #[DataProvider('providePrimitiveTypes')]
    #[Test]
    public function it_should_parse_primitive_types(Schema $type, string $expectedName): void
    {
        $parsedSchema = $type->parse();
        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
        $this->assertEquals($expectedName, $parsedSchema->type());
    }

    /**
     * @return array<string, array{0: Schema, 1: string}>
     */
    public static function providePrimitiveTypes(): array
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
