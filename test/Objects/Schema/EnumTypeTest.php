<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class EnumTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_serialize_enum_types(): void
    {
        $serializedEnum = Schema::enum()
            ->name('Suit')
            ->namespace('com.org.acme')
            ->aliases('outfit', 'elegant')
            ->doc('Suit up!')
            ->symbols(...['SPADES', 'HEARTS', 'DIAMONDS', 'CLUBS'])
            ->default('SPADES')
            ->serialize();

        $expectedEnum = [
            'type' => 'enum',
            'name' => 'Suit',
            'namespace' => 'com.org.acme',
            'aliases' => ['outfit', 'elegant'],
            'doc' => 'Suit up!',
            'symbols' => ['SPADES', 'HEARTS', 'DIAMONDS', 'CLUBS'],
            'default' => 'SPADES',
        ];

        $this->assertEquals($expectedEnum, $serializedEnum);
    }

    /**
     * @test
     */
    public function it_should_parse_enum_types(): void
    {
        $parsedSchema = Schema::enum()
            ->name('Suit')
            ->namespace('com.org.acme')
            ->aliases('outfit', 'elegant')
            ->doc('Suit up!')
            ->symbols(...['SPADES', 'HEARTS', 'DIAMONDS', 'CLUBS'])
            ->default('SPADES')
            ->parse();

        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
    }
}
