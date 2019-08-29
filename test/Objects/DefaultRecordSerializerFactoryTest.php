<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use PHPUnit\Framework\TestCase;

class DefaultRecordSerializerFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_produce_a_default_RecordSerializer(): void
    {
        DefaultRecordSerializerFactory::get('http://localhost');
    }
}
