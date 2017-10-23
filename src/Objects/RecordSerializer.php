<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use AvroSchema;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Registry;
use GuzzleHttp\Promise\PromiseInterface;
use const FlixTech\AvroSerializer\Common\get;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_AVRO;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_SCHEMA_ID;
use function FlixTech\AvroSerializer\Protocol\decode;
use function FlixTech\AvroSerializer\Protocol\encoder;
use function FlixTech\AvroSerializer\Serialize\avroDatumReader;
use function FlixTech\AvroSerializer\Serialize\avroDatumWriter;
use function Widmogrod\Functional\curryN;

class RecordSerializer
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var callable[]
     */
    private $writers = [];

    /**
     * @var callable[]
     */
    private $readers = [];

    /**
     * @var callable[]
     */
    private $encoders = [];

    /**
     * @var callable
     */
    private $datumWriterFactoryFunc;

    /**
     * @var callable
     */
    private $datumReaderFactoryFunc;

    /**
     * @var callable
     */
    private $protocolEncoderFactoryFunc;

    /**
     * @var callable
     */
    private $schemaIdGetter;

    /**
     * @var callable
     */
    private $avroBinaryGetter;

    /**
     * @var bool
     */
    private $registerMissingSchemas;

    public function __construct(Registry $registry, array $options = [])
    {
        $this->registry = $registry;

        $this->datumWriterFactoryFunc = avroDatumWriter();
        $this->datumReaderFactoryFunc = avroDatumReader();

        $this->protocolEncoderFactoryFunc = encoder();

        $get = curryN(2, get);

        $this->schemaIdGetter = $get(PROTOCOL_ACCESSOR_AVRO);
        $this->avroBinaryGetter = $get(PROTOCOL_ACCESSOR_SCHEMA_ID);

        $this->registerMissingSchemas = isset($options['register_missing_schemas'])
            ? (bool) $options['register_missing_schemas']
            : false;
    }

    public function encodeRecord(string $subject, AvroSchema $schema, $record): string
    {
        $schemaId = $this->getSchemaIdForSchema($subject, $schema);

        return ($this->getOrCreateCachedWriter($schema, $schemaId))($record)
            ->bind($this->getOrCreateCachedEncoder($schemaId))
            ->extract();
    }

    public function decodeMessage(string $message, AvroSchema $readersSchema = null)
    {
        $decoded = decode($message);
        $schemaId = $decoded->bind($this->schemaIdGetter)->extract();
        $writersSchema = $this->extractValueFromRegistryResponse($this->registry->schemaForId($schemaId));

        if (null === $readersSchema) {
            $readersSchema = $writersSchema;
        }

        return $decoded
            ->bind($this->avroBinaryGetter)
            ->bind(($this->getOrCreateCachedReader($schemaId, $writersSchema))($readersSchema))
            ->extract();
    }

    private function getSchemaIdForSchema(string $subject, AvroSchema $schema): int
    {
        try {
            $schemaId = $this->extractValueFromRegistryResponse($this->registry->schemaId($subject, $schema));
        } catch (SchemaNotFoundException $e) {
            if (!$this->registerMissingSchemas) {
                throw $e;
            }
            $schemaId = $this->extractValueFromRegistryResponse($this->registry->register($subject, $schema));
        }

        return $schemaId;
    }

    private function getOrCreateCachedWriter(AvroSchema $schema, int $schemaId): callable
    {
        if (!array_key_exists($schemaId, $this->writers)) {
            $this->writers[$schemaId] = call_user_func($this->datumWriterFactoryFunc, $schema);
        }

        return $this->writers[$schemaId];
    }

    private function getOrCreateCachedEncoder(int $schemaId): callable
    {
        if (!array_key_exists($schemaId, $this->encoders)) {
            $this->encoders[$schemaId] = call_user_func($this->protocolEncoderFactoryFunc, $schemaId);
        }

        return $this->encoders[$schemaId];
    }

    private function getOrCreateCachedReader(int $schemaId, AvroSchema $writersSchema): callable
    {
        if (!array_key_exists($schemaId, $this->readers)) {
            $this->readers[$schemaId] = call_user_func($this->datumReaderFactoryFunc, $writersSchema);
        }

        return $this->readers[$schemaId];
    }

    private function extractValueFromRegistryResponse($response)
    {
        if ($response instanceof PromiseInterface) {
            return $response->wait();
        }

        return $response;
    }
}
