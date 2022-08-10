<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test;

use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;

use const FlixTech\AvroSerializer\Common\get;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_AVRO;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_SCHEMA_ID;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_VERSION;

use function FlixTech\AvroSerializer\Common\get;
use function FlixTech\AvroSerializer\Common\getter;
use function Widmogrod\Functional\curryN;

class CommonTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     */
    public function get_should_return_Maybe_monad(): void
    {
        $array = [
            PROTOCOL_ACCESSOR_VERSION => 0,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
            PROTOCOL_ACCESSOR_AVRO => \hex2bin(self::HEX_BIN),
        ];

        $maybe = get(PROTOCOL_ACCESSOR_SCHEMA_ID, $array);

        $this->assertInstanceOf(Just::class, $maybe);
        $this->assertSame(self::SCHEMA_ID, $maybe->extract());
        $this->assertInstanceOf(Nothing::class, get('INVALID', $array));
    }

    /**
     * @test
     */
    public function getter_returns_curried_get(): void
    {
        $this->assertEquals(
            curryN(2, get),
            getter()
        );
    }
}
