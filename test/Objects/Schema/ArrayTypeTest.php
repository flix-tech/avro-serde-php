<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class ArrayTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_serialize_array_types(): void
    {
        $serializedArray = Schema::array()
            ->items(Schema::string())
            ->default(['foo', 'bar'])
            ->serialize();

        $expectedArray = [
            'type' => 'array',
            'items' => 'string',
            'default' => ['foo', 'bar'],
        ];

        $this->assertEquals($expectedArray, $serializedArray);
    }

    /**
     * @test
     */
    public function it_should_parse_array_types(): void
    {
        $parsedSchema = Schema::array()
            ->items(Schema::string())
            ->default(['foo', 'bar'])
            ->parse();

        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
        $this->assertEquals('array', $parsedSchema->type());
    }
}
