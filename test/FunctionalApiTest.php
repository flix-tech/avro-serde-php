<?php

namespace FlixTech\AvroSerializer\Test;

use AvroSchema;
use PHPUnit\Framework\TestCase;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_AVRO;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_SCHEMA_ID;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_VERSION;
use function FlixTech\AvroSerializer\Common\get;
use function FlixTech\AvroSerializer\Protocol\decode;
use function FlixTech\AvroSerializer\Protocol\encoder;
use function FlixTech\AvroSerializer\Protocol\version;
use function FlixTech\AvroSerializer\Serialize\avroDatumReader;
use function FlixTech\AvroSerializer\Serialize\avroDatumWriter;

class FunctionalApiTest extends TestCase
{
    const SCHEMA_JSON = /** @lang JSON */
        <<<JSON
{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "name", "type": "string"},
    {"name": "age", "type": "int"}
  ]
}
JSON;

    const TEST_RECORD = [
        'name' => 'Thomas',
        'age' => 36,
    ];

    const HEX_BIN = '000000270f0c54686f6d617348';
    const SCHEMA_ID = 9999;

    /**
     * @var AvroSchema
     */
    private $avroSchema;

    protected function setUp()
    {
        $this->avroSchema = AvroSchema::parse(self::SCHEMA_JSON);
    }

    /**
     * @test
     */
    public function protocol_encoder_should_encode_correctly()
    {
        $encoderFuncWithSchemaId = encoder()(self::SCHEMA_ID);
        $writerWithEmbeddedSchemaFunc = avroDatumWriter()($this->avroSchema);

        $encoded = $writerWithEmbeddedSchemaFunc(self::TEST_RECORD)
            ->bind($encoderFuncWithSchemaId);

        $this->assertInstanceOf(Either::class, $encoded);
        $this->assertSame(self::HEX_BIN, bin2hex($encoded->extract()));
    }

    /**
     * @test
     */
    public function protocol_decoder_should_decode_correctly()
    {
        $binaryInput = hex2bin(self::HEX_BIN);
        $decoded = decode($binaryInput);

        $this->assertInstanceOf(Either::class, $decoded);

        $unpacked = $decoded->extract();

        $this->assertSame(self::SCHEMA_ID, $unpacked[PROTOCOL_ACCESSOR_SCHEMA_ID]);
        $this->assertSame(version(), $unpacked[PROTOCOL_ACCESSOR_VERSION]);
        $this->assertNotEmpty($unpacked[PROTOCOL_ACCESSOR_AVRO]);

        $decodedRecord = avroDatumReader()($this->avroSchema)($this->avroSchema)($unpacked[PROTOCOL_ACCESSOR_AVRO]);

        $this->assertInstanceOf(Either::class, $decodedRecord);
        $this->assertEquals(self::TEST_RECORD, $decodedRecord->extract());
    }

    /**
     * @test
     */
    public function get_should_return_maybe_monad()
    {
        $array = [
            PROTOCOL_ACCESSOR_VERSION => 0,
            PROTOCOL_ACCESSOR_SCHEMA_ID => self::SCHEMA_ID,
            PROTOCOL_ACCESSOR_AVRO => hex2bin(self::HEX_BIN),
        ];

        $schemaId = get(PROTOCOL_ACCESSOR_SCHEMA_ID, $array);
        $this->assertInstanceOf(Just::class, $schemaId);

        $this->assertInstanceOf(Nothing::class, get('INVALID', $array));
    }
}
