<?php

namespace FlixTech\AvroSerializer\Test;

use AvroIODatumWriter;
use FlixTech\AvroSerializer\Objects\Exceptions\AvroDecodingException;
use FlixTech\AvroSerializer\Objects\Exceptions\AvroEncodingException;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

use const FlixTech\AvroSerializer\Serialize\writeDatum;

use function FlixTech\AvroSerializer\Serialize\avroBinaryDecoder;
use function FlixTech\AvroSerializer\Serialize\avroBinaryEncoder;
use function FlixTech\AvroSerializer\Serialize\avroDatumReader;
use function FlixTech\AvroSerializer\Serialize\avroDatumWriter;
use function FlixTech\AvroSerializer\Serialize\avroStringIo;
use function FlixTech\AvroSerializer\Serialize\readDatum;
use function FlixTech\AvroSerializer\Serialize\writeDatum;
use function Widmogrod\Functional\curryN;

class SerializeTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function avroStringIo_should_produce_new_instances_of_AvroStringIO(): void
    {
        $avroStringIo = avroStringIo('test');
        $instance = new \AvroStringIO('test');

        $this->assertEquals($instance, $avroStringIo);
        $this->assertNotSame($instance, $avroStringIo);
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function avroBinaryEncoder_should_produce_new_instances_of_AvroBinaryEncoder(): void
    {
        $avroStringIo = avroStringIo('test');

        $binaryEncoder = avroBinaryEncoder($avroStringIo);
        $instance = new \AvroIOBinaryEncoder($avroStringIo);

        $this->assertEquals($instance, $binaryEncoder);
        $this->assertNotSame($instance, $binaryEncoder);
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function avroBinaryDecoder_should_produce_new_instances_of_AvroBinaryDecoder(): void
    {
        $avroStringIo = avroStringIo('test');

        $binaryDecoder = avroBinaryDecoder($avroStringIo);
        $instance = new \AvroIOBinaryDecoder($avroStringIo);

        $this->assertEquals($instance, $binaryDecoder);
        $this->assertNotSame($instance, $binaryDecoder);
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function avroDatumWriter_should_create_curried_function(): void
    {
        $writer = new AvroIODatumWriter();
        $io = avroStringIo('');

        $this->assertEquals(
            curryN(4, writeDatum)($writer)($io),
            avroDatumWriter()
        );
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function writeDatum_should_correctly_produce_avro_encoded_binary_string_Right_Monad(): void
    {
        $writer = new AvroIODatumWriter();
        $io = avroStringIo('');

        $firstCall = writeDatum($writer, $io, $this->avroSchema, self::TEST_RECORD);

        $this->assertInstanceOf(Right::class, $firstCall);
        $this->assertSame(self::AVRO_ENCODED_RECORD_HEX_BIN, \bin2hex($firstCall->extract()));

        $secondCall = writeDatum($writer, $io, $this->avroSchema, self::TEST_RECORD);

        $this->assertInstanceOf(Right::class, $secondCall);
        $this->assertSame(self::AVRO_ENCODED_RECORD_HEX_BIN, \bin2hex($secondCall->extract()));

        $this->assertEquals($firstCall, $secondCall);
        $this->assertNotSame($firstCall, $secondCall);
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function writeDatum_should_produce_Left_Monad_for_invalid_inputs(): void
    {
        $writer = new AvroIODatumWriter();
        $io = avroStringIo('');

        $left = writeDatum($writer, $io, $this->avroSchema, self::INVALID_TEST_RECORD);

        $this->assertInstanceOf(Left::class, $left);
        $this->assertInstanceOf(AvroEncodingException::class, $left->extract());
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function avroDatumReader_should_return_curried_function(): void
    {
        $writer = new \AvroIODatumReader();
        $io = avroStringIo('');

        $this->assertEquals(
            curryN(5, writeDatum)($writer)($io),
            avroDatumReader()
        );
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function readDatum_should_return_Right_Monad_for_valid_inputs(): void
    {
        $reader = new \AvroIODatumReader();
        $io = avroStringIo('');
        $data = \hex2bin(self::AVRO_ENCODED_RECORD_HEX_BIN);

        $firstCall = readDatum($reader, $io, $this->avroSchema, $this->avroSchema, $data);

        $this->assertInstanceOf(Right::class, $firstCall);
        $this->assertEquals(self::TEST_RECORD, $firstCall->extract());

        $secondCall = readDatum($reader, $io, $this->avroSchema, $this->avroSchema, $data);

        $this->assertInstanceOf(Right::class, $secondCall);
        $this->assertEquals(self::TEST_RECORD, $secondCall->extract());

        $readerSchemaCall = readDatum($reader, $io, $this->avroSchema, $this->readersSchema, $data);

        $this->assertInstanceOf(Right::class, $readerSchemaCall);
        $this->assertEquals(self::READERS_TEST_RECORD, $readerSchemaCall->extract());
    }

    /**
     * @test
     *
     * @throws \AvroIOException
     */
    public function readDatum_should_turn_Left_Monad_for_invalid_reader_and_writer_schemas(): void
    {
        $reader = new \AvroIODatumReader();
        $io = avroStringIo('');
        $data = \hex2bin(self::INVALID_AVRO_ENCODED_RECORD_HEX_BIN);

        $firstCall = readDatum($reader, $io, $this->avroSchema, $this->invalidSchema, $data);

        $this->assertInstanceOf(Left::class, $firstCall);
        $this->assertInstanceOf(AvroDecodingException::class, $firstCall->extract());
    }
}
