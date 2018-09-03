<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use PHPUnit\Framework\TestCase;

class DefaultRecordSerializerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_produce_a_default_RecordSerializer(): void
    {
        $serializer = DefaultRecordSerializerFactory::get('http://localhost');

        $this->assertInstanceOf(RecordSerializer::class, $serializer);
    }
}
