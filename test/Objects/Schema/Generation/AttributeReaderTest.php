<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Exceptions\UnsupportedPhpVersionException;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AttributeReader;
use PHPUnit\Framework\TestCase;

class AttributeReaderTest extends TestCase
{
    /**
     * @test
     * @requires PHP < 8.1
     */
    public function it_should_throw_exception_when_the_php_version_is_not_supported(): void
    {
        $this->expectException(UnsupportedPhpVersionException::class);

        new AttributeReader();
    }
}
