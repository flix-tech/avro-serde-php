<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use FlixTech\AvroSerializer\Objects\Exceptions\AvroEncodingException;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\AvroSerializer\Test\AbstractFunctionalTestCase;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException;
use FlixTech\SchemaRegistryApi\Registry;
use GuzzleHttp\Promise\FulfilledPromise;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;

use function FlixTech\AvroSerializer\Common\memoize;

class RecordSerializerTest extends AbstractFunctionalTestCase
{
    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var RecordSerializer
     */
    private $recordSerializer;

    /**
     * @throws \ReflectionException
     * @throws \AvroSchemaParseException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->registryMock = $this->getMockForAbstractClass(Registry::class);
        $this->recordSerializer = new RecordSerializer($this->registryMock);
    }

    protected function tearDown(): void
    {
        // Clear memoization between tests
        memoize();
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_encode_a_record_with_schema_and_subject(): void
    {
        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaId')
            ->withConsecutive(['test', $this->avroSchema], ['test', $this->avroSchema])
            ->willReturnOnConsecutiveCalls(self::SCHEMA_ID, new FulfilledPromise(self::SCHEMA_ID));

        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_throw_encoding_exception_on_invalid_schema(): void
    {
        $this->expectException(AvroEncodingException::class);
        $this->expectExceptionCode(501);
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
     * @throws SchemaRegistryException
     */
    public function it_should_not_register_new_schemas_by_default(): void
    {
        $this->expectException(SchemaNotFoundException::class);
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
     *
     * @throws SchemaRegistryException
     */
    public function it_should_register_new_schemas_when_configured(): void
    {
        $recordSerializer = new RecordSerializer($this->registryMock, ['register_missing_schemas' => true]);

        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new SchemaNotFoundException());

        $this->registryMock
            ->expects($this->exactly(2))
            ->method('register')
            ->withConsecutive(['test', $this->avroSchema], ['test', $this->avroSchema])
            ->willReturn(self::SCHEMA_ID, new FulfilledPromise(self::SCHEMA_ID));

        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_fail_when_the_subject_is_not_found(): void
    {
        $this->expectException(SubjectNotFoundException::class);
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
     * @throws SchemaRegistryException
     */
    public function it_should_register_new_subject_when_configured(): void
    {
        $recordSerializer = new RecordSerializer($this->registryMock, ['register_missing_subjects' => true]);

        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new SubjectNotFoundException());

        $this->registryMock
            ->expects($this->exactly(2))
            ->method('register')
            ->withConsecutive(['test', $this->avroSchema], ['test', $this->avroSchema])
            ->willReturnOnConsecutiveCalls(self::SCHEMA_ID, new FulfilledPromise(self::SCHEMA_ID));

        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::HEX_BIN,
            \bin2hex($recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD))
        );
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_fail_when_the_subject_is_not_found_via_promise(): void
    {
        $this->expectException(SubjectNotFoundException::class);
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
     *
s     * @throws SchemaRegistryException
     */
    public function it_should_fail_when_an_unexpected_exception_is_wrapped_in_a_promise(): void
    {
        $this->expectException(LogicException::class);
        $this->registryMock
            ->expects($this->once())
            ->method('schemaId')
            ->with('test', $this->avroSchema)
            ->willThrowException(new LogicException());

        $this->registryMock
            ->expects($this->never())
            ->method('register');

        $this->recordSerializer->encodeRecord('test', $this->avroSchema, self::TEST_RECORD);
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_decode_wire_protocol_messages_correctly(): void
    {
        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaForId')
            ->withConsecutive([self::SCHEMA_ID], [self::SCHEMA_ID])
            ->willReturnOnConsecutiveCalls($this->avroSchema, new FulfilledPromise($this->avroSchema));

        $this->assertSame(
            self::TEST_RECORD,
            $this->recordSerializer->decodeMessage(\hex2bin(self::HEX_BIN))
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::TEST_RECORD,
            $this->recordSerializer->decodeMessage(\hex2bin(self::HEX_BIN))
        );
    }

    /**
     * @test
     *
     * @throws SchemaRegistryException
     */
    public function it_should_decode_with_readers_schema(): void
    {
        $this->registryMock
            ->expects($this->exactly(2))
            ->method('schemaForId')
            ->withConsecutive([self::SCHEMA_ID], [self::SCHEMA_ID])
            ->willReturnOnConsecutiveCalls($this->avroSchema, new FulfilledPromise($this->avroSchema));

        $this->assertSame(
            self::READERS_TEST_RECORD,
            $this->recordSerializer->decodeMessage(\hex2bin(self::HEX_BIN), $this->readersSchema)
        );

        // Second call to ensure memoized functions work as expected
        $this->assertSame(
            self::READERS_TEST_RECORD,
            $this->recordSerializer->decodeMessage(\hex2bin(self::HEX_BIN), $this->readersSchema)
        );
    }
}
