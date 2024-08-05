<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use PHPUnit\Framework\Attributes\Test;
use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use PHPUnit\Framework\TestCase;

class DefaultRecordSerializerFactoryTest extends TestCase
{
    #[Test]
    public function it_should_produce_a_default_RecordSerializer(): void
    {
        $one = DefaultRecordSerializerFactory::get('http://localhost');
        $two = DefaultRecordSerializerFactory::get('http://localhost');

        $this->assertNotSame($one, $two);
    }
}
