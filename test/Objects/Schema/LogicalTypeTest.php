<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class LogicalTypeTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideLogicalTypes()
     */
    public function it_should_serialize_simple_logical_types(Schema $type, string $expectedAnnotatedType, string $expectedLogicalType)
    {
        $expectedSchema = [
            'type' => $expectedAnnotatedType,
            'logicalType' => $expectedLogicalType,
        ];

        $this->assertEquals($expectedSchema, $type->serialize());
    }

    /**
     * @test
     * @dataProvider provideLogicalTypes()
     */
    public function it_should_parse_simple_logical_types(Schema $type, string $expectedType, string $expectedLogicalType)
    {
        $parsedSchema = $type->parse();
        $this->assertEquals($expectedType, $parsedSchema->type());
        $this->assertEquals($expectedLogicalType, $parsedSchema->logical_type());
    }

    /**
     * @test
     */
    public function it_should_serialize_duration_types()
    {
        $schema = Schema::duration()
            ->name('User')
            ->namespace('org.acme')
            ->aliases('foobar')
            ->serialize();

        $expected = [
            'logicalType' => 'duration',
            'type' => 'fixed',
            'name' => 'User',
            'namespace' => 'org.acme',
            'aliases' => ['foobar'],
            'size' => 12,
        ];

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_parse_duration_types()
    {
        $parsedSchema = Schema::duration()
            ->name('User')
            ->namespace('org.acme')
            ->aliases('foobar')
            ->parse();

        $this->assertEquals('fixed', $parsedSchema->type());
        $this->assertEquals('duration', $parsedSchema->logical_type());
    }

    public function provideLogicalTypes(): array
    {
        return [
            'uuid' => [Schema::uuid(), 'string', 'uuid'],
            'date' => [Schema::date(), 'int', 'date'],
            'time-millis' => [Schema::timeMillis(), 'int', 'time-millis'],
            'time-micros' => [Schema::timeMicros(), 'long', 'time-micros'],
            'timestamp-millis' => [Schema::timestampMillis(), 'long', 'timestamp-millis'],
            'timestamp-micros' => [Schema::timestampMicros(), 'long', 'timestamp-micros'],
            'local-timestamp-millis' => [Schema::localTimestampMillis(), 'long', 'local-timestamp-millis'],
            'local-timestamp-micros' => [Schema::localTimestampMicros(), 'long', 'local-timestamp-micros'],
        ];
    }
}
