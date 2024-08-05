<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Integrations\Symfony\Serializer;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\AvroSerializer\Test\AbstractFunctionalTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;

class AvroSerDeEncoderTest extends AbstractFunctionalTestCase
{
    /**
     * @var RecordSerializer|MockObject
     */
    private $recordSerializerMock;

    /**
     * @var AvroSerDeEncoder
     */
    private $avroSerDeEncoder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->recordSerializerMock = $this->getMockBuilder(RecordSerializer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->avroSerDeEncoder = new AvroSerDeEncoder($this->recordSerializerMock);
    }

    #[Test]
    public function it_should_only_support_encoding_Avro_format(): void
    {
        $this->assertTrue($this->avroSerDeEncoder->supportsEncoding(AvroSerDeEncoder::FORMAT_AVRO));
        $this->assertFalse($this->avroSerDeEncoder->supportsEncoding('any'));
    }

    #[Test]
    public function it_should_only_support_decoding_Avro_format(): void
    {
        $this->assertTrue($this->avroSerDeEncoder->supportsDecoding(AvroSerDeEncoder::FORMAT_AVRO));
        $this->assertFalse($this->avroSerDeEncoder->supportsDecoding('any'));
    }

    #[Test]
    public function it_should_encode_with_valid_encode_context(): void
    {
        $context = [
            AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => $this->avroSchema,
            AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'test',
        ];

        $this->recordSerializerMock->expects($this->once())
            ->method('encodeRecord')
            ->with('test', $this->avroSchema, AbstractFunctionalTestCase::TEST_RECORD)
            ->willReturn('success');

        $result = $this->avroSerDeEncoder->encode(
            AbstractFunctionalTestCase::TEST_RECORD,
            AvroSerDeEncoder::FORMAT_AVRO,
            $context
        );

        $this->assertSame('success', $result);
    }

    #[Test]
    public function it_should_decode_with_valid_decode_context(): void
    {
        $matcher = $this->exactly(2);
        $this->recordSerializerMock->expects($matcher)
            ->method('decodeMessage')->willReturnCallback(function ($parameters) use ($matcher) {
            match ($matcher->numberOfInvocations()) {
                1 => self::assertEquals([AbstractFunctionalTestCase::AVRO_ENCODED_RECORD_HEX_BIN, null], $parameters),
                2 => self::assertEquals([AbstractFunctionalTestCase::AVRO_ENCODED_RECORD_HEX_BIN, $this->readersSchema], $parameters),
            };
            return match ($matcher->numberOfInvocations()) {
                1 => 'success-1',
                2 => 'success-2',
            };
        });

        $result = $this->avroSerDeEncoder->decode(
            AbstractFunctionalTestCase::AVRO_ENCODED_RECORD_HEX_BIN,
            AvroSerDeEncoder::FORMAT_AVRO
        );

        $this->assertSame('success-1', $result);

        $result = $this->avroSerDeEncoder->decode(
            AbstractFunctionalTestCase::AVRO_ENCODED_RECORD_HEX_BIN,
            AvroSerDeEncoder::FORMAT_AVRO,
            [
                AvroSerDeEncoder::CONTEXT_DECODE_READERS_SCHEMA => $this->readersSchema,
            ]
        );

        $this->assertSame('success-2', $result);
    }

    #[DataProvider('encodeContextValidationDataProvider')]
    #[Test]
    public function it_should_validate_encode_context(array $context): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->recordSerializerMock->expects($this->never())
            ->method('encodeRecord');

        $this->avroSerDeEncoder->encode(
            AbstractFunctionalTestCase::TEST_RECORD,
            AvroSerDeEncoder::FORMAT_AVRO,
            $context
        );
    }

    public static function encodeContextValidationDataProvider(): \Generator
    {
        yield 'Invalid writer\'s schema in encode context' => [
            [
                AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => new \stdClass(),
                AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'test',
            ],
        ];

        yield 'Missing subject in encode context' => [
            [
                AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => \AvroSchema::parse(
                    AbstractFunctionalTestCase::SCHEMA_JSON
                ),
            ],
        ];

        yield 'Invalid type for subject in encode context' => [
            [
                AvroSerDeEncoder::CONTEXT_ENCODE_WRITERS_SCHEMA => \AvroSchema::parse(
                    AbstractFunctionalTestCase::SCHEMA_JSON
                ),
                AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 42,
            ],
        ];

        yield 'Missing writer\'s schema in encode context' => [
            [
                AvroSerDeEncoder::CONTEXT_ENCODE_SUBJECT => 'test',
            ],
        ];
    }
}
