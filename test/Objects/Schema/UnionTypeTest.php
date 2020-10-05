<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use PHPUnit\Framework\TestCase;

class UnionTypeTest extends TestCase
{
    public function testShouldSerializeUnionTypes(): void
    {
        $serializedUnion = Schema::union(
            Schema::null(),
            Schema::string(),
            Schema::long()
        )->serialize();

        $expectedUnion = ['null', 'string', 'long'];

        $this->assertEquals($expectedUnion, $serializedUnion);
    }
}
