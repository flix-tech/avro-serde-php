<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\AvroSerializer\Test\AbstractFunctionalTestCase;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException;
use FlixTech\SchemaRegistryApi\Registry;
use GuzzleHttp\Promise\FulfilledPromise;
use function FlixTech\AvroSerializer\Common\memoize;

class RecordSerializerTest extends AbstractFunctionalTestCase
{
    /**
     * @var \FlixTech\SchemaRegistryApi\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registryMock;

    /**
     * @var RecordSerializer
     */
    private $recordSerializer;

    protected function setUp()
    {
        parent::setUp();

        $this->registryMock = $this->getMockForAbstractClass(Registry::class);
        $this->recordSerializer = new RecordSerializer($this->registryMock);
    }

    /**
     * @test
     */
    public function it_should_encode_a_record_with_schema_and_subject()
    {
        $this->registryMock
            ->expects($this->at(0))
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willReturn(self::SCHEMA_ID);

        $this->registryMock
            ->expects($this->at(1))
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willReturn(new FulfilledPromise(self::SCHEMA_ID));

        $this->assertSame(
            self::HEX_BIN,
            bin2hex($this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::HEX_BIN,
            bin2hex($this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );
    }

    /**
     * @test
     *
     * @expectedException \FlixTech\AvroSerializer\Objects\Exceptions\AvroEncodingException
     * @expectedExceptionCode 501
     */
    public function it_should_throw_encoding_exception_on_invalid_schema()
    {
        $this->registryMock
            ->expects($this->once())
            ->method('schemaId')
            ->with('test', $this->invalidSchema)
            ->willReturn(self::SCHEMA_ID);

        $this->recordSerializer->encodeRecord('test', $this->invalidSchema, self::TEST_RECORD);
    }

    /**
     * @test
     *
     * @expectedException \FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException
     */
    public function it_should_not_register_new_schemas_by_default()
    {
        $this->registryMock
            ->expects($this->once())
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new SchemaNotFoundException());

        $this->registryMock
            ->expects($this->never())
            ->method('register');

        $this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD);
    }

    /**
     * @test
     */
    public function it_should_register_new_schemas_when_configured()
    {
        $recordSerializer = new RecordSerializer($this->registryMock, ['register_missing_schemas' => true]);

        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new SchemaNotFoundException());

        $this->registryMock
            ->expects($this->at(1))
            ->method('register')
            ->with('test', $this->avroSchema)
            ->willReturn(self::SCHEMA_ID);

        $this->registryMock
            ->expects($this->at(3))
            ->method('register')
            ->with('test', $this->avroSchema)
            ->willReturn(new FulfilledPromise(self::SCHEMA_ID));

        $this->assertSame(
            self::HEX_BIN,
            bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::HEX_BIN,
            bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );
    }

    /**
     * @test
     *
     * @expectedException \FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException
     */
    public function it_should_fail_when_the_subject_is_not_found()
    {
        $this->registryMock
            ->expects($this->once())
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new SubjectNotFoundException());

        $this->registryMock
            ->expects($this->never())
            ->method('register');

        $this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD);
    }

    /**
     * @test
     *
     * @expectedException \FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException
     */
    public function it_should_fail_when_the_subject_is_not_found_via_promise()
    {
        $this->registryMock
            ->expects($this->once())
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willReturn(new FulfilledPromise(new SubjectNotFoundException()));

        $this->registryMock
            ->expects($this->never())
            ->method('register');

        $this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD);
    }

    /**
     * @test
     */
    public function it_should_decode_wire_protocol_messages_correctly()
    {
        $this->registryMock
            ->expects($this->at(0))
            ->method('schemaForId')
            ->with(self::SCHEMA_ID)
            ->willReturn($this->avroSchema);

        $this->registryMock
            ->expects($this->at(1))
            ->method('schemaForId')
            ->with(self::SCHEMA_ID)
            ->willReturn(new FulfilledPromise($this->avroSchema));

        $this->assertSame(
            self::TEST_RECORD,
            $this->recordSerializer->decodeMessage(hex2bin(self::HEX_BIN))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::TEST_RECORD,
            $this->recordSerializer->decodeMessage(hex2bin(self::HEX_BIN))
        );
    }

    /**
     * @test
     */
    public function it_should_decode_with_readers_schema()
    {
        $this->registryMock
            ->expects($this->at(0))
            ->method('schemaForId')
            ->with(self::SCHEMA_ID)
            ->willReturn($this->avroSchema);

        $this->registryMock
            ->expects($this->at(1))
            ->method('schemaForId')
            ->with(self::SCHEMA_ID)
            ->willReturn(new FulfilledPromise($this->avroSchema));

        $this->assertSame(
            self::READERS_TEST_RECORD,
            $this->recordSerializer->decodeMessage(hex2bin(self::HEX_BIN), $this->readersSchema)
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::READERS_TEST_RECORD,
            $this->recordSerializer->decodeMessage(hex2bin(self::HEX_BIN), $this->readersSchema)
        );
    }

    protected function tearDown()
    {
        // Clear memoization between tests
        memoize();
    }
}
