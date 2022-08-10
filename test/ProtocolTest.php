<?php

namespace FlixTech\AvroSerializer\Test;

use FlixTech\AvroSerializer\Objects\Exceptions\AvroDecodingException;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;

use const FlixTech\AvroSerializer\Protocol\encode;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_AVRO;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_SCHEMA_ID;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_VERSION;
use const FlixTech\AvroSerializer\Protocol\validate;
use const FlixTech\AvroSerializer\Protocol\WIRE_FORMAT_PROTOCOL_VERSION;

use function FlixTech\AvroSerializer\Protocol\decode;
use function FlixTech\AvroSerializer\Protocol\encode;
use function FlixTech\AvroSerializer\Protocol\encoder;
use function FlixTech\AvroSerializer\Protocol\validate;
use function FlixTech\AvroSerializer\Protocol\validator;
use function FlixTech\AvroSerializer\Protocol\version;
use function Widmogrod\Functional\curryN;

class ProtocolTest extends AbstractFunctionalTestCase
{
    private const NULL_TERMINATED_HEX = '000000270f303000000000';
    private const NULL_TERMINATED_AVRO_HEX = '303000000000';

    /**
     * @test
     */
    public function it_should_always_provide_correct_version(): void
    {
        $this->assertSame(version(), WIRE_FORMAT_PROTOCOL_VERSION);
    }

    /**
     * @test
     */
    public function encode_should_produce_Right_Either_Monad_with_valid_protocol(): void
    {
        $encoded = encode(WIRE_FORMAT_PROTOCOL_VERSION, self::SCHEMA_ID, self::HEX_BIN);

        $this->assertInstanceOf(Right::class, $encoded);
        $this->assertSame(self::VALID_PROTOCOL_HEX_BIN, \bin2hex($encoded->extract()));
    }

    /**
     * @test
     */
    public function encoder_factory_should_create_curried_function(): void
    {
        $encoder = encoder(WIRE_FORMAT_PROTOCOL_VERSION);

        $this->assertEquals(
            curryN(3, encode)(WIRE_FORMAT_PROTOCOL_VERSION),
            $encoder
        );
    }

    /**
     * @test
     */
    public function protocol_decoder_should_decode_correctly(): void
    {
        $binaryInput = \hex2bin(self::HEX_BIN);
        $decoded = decode($binaryInput);

        $this->assertInstanceOf(Right::class, $decoded);

        $unpacked = $decoded->extract();

        $this->assertTrue(\is_array($unpacked));
        $this->assertSame(WIRE_FORMAT_PROTOCOL_VERSION, $unpacked[PROTOCOL_ACCESSOR_VERSION]);
        $this->assertSame(self::SCHEMA_ID, $unpacked[PROTOCOL_ACCESSOR_SCHEMA_ID]);
        $this->assertSame(self::AVRO_ENCODED_RECORD_HEX_BIN, \bin2hex($unpacked[PROTOCOL_ACCESSOR_AVRO]));
    }

    /**
     * @test
     */
    public function protocol_decoder_should_turn_Left_with_Exception_for_invalid_inputs(): void
    {
        $binaryInput = \hex2bin(self::INVALID_BIN_TOO_SHORT);
        $decoded = decode($binaryInput);

        $this->assertInstanceOf(Left::class, $decoded);
        $this->assertInstanceOf(AvroDecodingException::class, $decoded->extract());
    }

    /**
     * @test
     */
    public function validator_factory_should_return_curried_function(): void
    {
        $this->assertEquals(
            curryN(2, validate)(WIRE_FORMAT_PROTOCOL_VERSION),
            validator(WIRE_FORMAT_PROTOCOL_VERSION)
        );
    }

    /**
     * @test
     */
    public function validate_should_inspect_unpacked_array_correctly(): void
    {
        $decoded = [
            PROTOCOL_ACCESSOR_VERSION => WIRE_FORMAT_PROTOCOL_VERSION,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
            PROTOCOL_ACCESSOR_AVRO => \hex2bin(self::AVRO_ENCODED_RECORD_HEX_BIN),
        ];

        $just = validate(WIRE_FORMAT_PROTOCOL_VERSION, $decoded);

        $this->assertInstanceOf(Just::class, $just);
        $this->assertSame($decoded, $just->extract());
    }

    /**
     * @test
     */
    public function validate_returns_nothing_for_invalid_unpacked(): void
    {
        $decoded = [
            PROTOCOL_ACCESSOR_VERSION => 1,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
            PROTOCOL_ACCESSOR_AVRO => \hex2bin(self::AVRO_ENCODED_RECORD_HEX_BIN),
        ];

        $this->assertInstanceOf(Nothing::class, validate(WIRE_FORMAT_PROTOCOL_VERSION, $decoded));

        $decoded = [
            PROTOCOL_ACCESSOR_VERSION => WIRE_FORMAT_PROTOCOL_VERSION,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
        ];

        $this->assertInstanceOf(Nothing::class, validate(WIRE_FORMAT_PROTOCOL_VERSION, $decoded));

        $decoded = [
            PROTOCOL_ACCESSOR_VERSION => WIRE_FORMAT_PROTOCOL_VERSION,
            PROTOCOL_ACCESSOR_SCHEMA_ID => 'INVALID',
            PROTOCOL_ACCESSOR_AVRO => \hex2bin(self::AVRO_ENCODED_RECORD_HEX_BIN),
        ];

        $this->assertInstanceOf(Nothing::class, validate(WIRE_FORMAT_PROTOCOL_VERSION, $decoded));

        $decoded = [
            PROTOCOL_ACCESSOR_VERSION => WIRE_FORMAT_PROTOCOL_VERSION,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
            PROTOCOL_ACCESSOR_AVRO => 1234,
        ];

        $this->assertInstanceOf(Nothing::class, validate(WIRE_FORMAT_PROTOCOL_VERSION, $decoded));
    }

    /**
     * @test
     */
    public function null_terminated_values_unpacked_correctly(): void
    {
        $decoded = decode(\hex2bin(self::NULL_TERMINATED_HEX));

        $this->assertInstanceOf(Right::class, $decoded);

        $unpacked = $decoded->extract();

        $this->assertIsArray($unpacked);
        $this->assertSame(WIRE_FORMAT_PROTOCOL_VERSION, $unpacked[PROTOCOL_ACCESSOR_VERSION]);
        $this->assertSame(self::SCHEMA_ID, $unpacked[PROTOCOL_ACCESSOR_SCHEMA_ID]);
        $this->assertSame(self::NULL_TERMINATED_AVRO_HEX, \bin2hex($unpacked[PROTOCOL_ACCESSOR_AVRO]));
    }
}
