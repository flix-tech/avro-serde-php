<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class FixedTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_serialize_fixed_types(): void
    {
        $serializedFixedType = Schema::fixed()
            ->namespace('org.acme')
            ->name('md5')
            ->size(16)
            ->aliases('hash', 'fileHash')
            ->serialize();

        $expectedFixedType = [
            'type' => 'fixed',
            'namespace' => 'org.acme',
            'name' => 'md5',
            'size' => 16,
            'aliases' => ['hash', 'fileHash'],
        ];

        $this->assertEquals($expectedFixedType, $serializedFixedType);
    }

    /**
     * @test
     */
    public function it_should_parse_fixed_types(): void
    {
        $parsedSchema = Schema::fixed()
            ->namespace('org.acme')
            ->name('md5')
            ->size(16)
            ->aliases('hash', 'fileHash')
            ->parse();

        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
        $this->assertEquals('fixed', $parsedSchema->type());
    }
}
