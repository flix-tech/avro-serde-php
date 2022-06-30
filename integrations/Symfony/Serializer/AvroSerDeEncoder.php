<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Integrations\Symfony\Serializer;

use Assert\Assert;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

class AvroSerDeEncoder implements EncoderInterface, DecoderInterface
{
    public const FORMAT_AVRO = 'avro_with_registry';

    public const CONTEXT_ENCODE_WRITERS_SCHEMA = self:: FORMAT_AVRO . '.writers_schema';
    public const CONTEXT_ENCODE_SUBJECT = self::FORMAT_AVRO . '.subject';
    public const CONTEXT_DECODE_READERS_SCHEMA = self::FORMAT_AVRO . '.readers_schema';

    /**
     * @var \FlixTech\AvroSerializer\Objects\RecordSerializer
     */
    private $recordSerializer;

    public function __construct(RecordSerializer $recordSerializer)
    {
        $this->recordSerializer = $recordSerializer;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function decode($data, $format, array $context = [])
    {
        $readersSchema = $context[self::CONTEXT_DECODE_READERS_SCHEMA] ?? null;
        Assert::that($readersSchema)->nullOr()->isInstanceOf(\AvroSchema::class);

        return $this->recordSerializer->decodeMessage($data, $readersSchema);
    }

    public function supportsDecoding($format): bool
    {
        return self::FORMAT_AVRO === $format;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \AvroSchemaParseException
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function encode($data, $format, array $context = []): string
    {
        $this->validateEncodeContext($context);

        return $this->recordSerializer->encodeRecord(
            $context[self::CONTEXT_ENCODE_SUBJECT],
            \AvroSchema::parse($context[self::CONTEXT_ENCODE_WRITERS_SCHEMA]),
            $data
        );
    }

    public function supportsEncoding($format): bool
    {
        return self::FORMAT_AVRO === $format;
    }

    private function validateEncodeContext(array $context): void
    {
        Assert::that($context)
            ->keyIsset(self::CONTEXT_ENCODE_WRITERS_SCHEMA)
            ->keyIsset(self::CONTEXT_ENCODE_SUBJECT);

        Assert::that($context[self::CONTEXT_ENCODE_WRITERS_SCHEMA])->isInstanceOf(\AvroSchema::class);
        Assert::that($context[self::CONTEXT_ENCODE_SUBJECT])
            ->string()
            ->notBlank()
            ->notEmpty();
    }
}
