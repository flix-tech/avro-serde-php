<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use AvroSchema;
use FlixTech\SchemaRegistryApi\Exception\SchemaNotFoundException;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use FlixTech\SchemaRegistryApi\Exception\SubjectNotFoundException;
use FlixTech\SchemaRegistryApi\Registry;
use GuzzleHttp\Promise\PromiseInterface;

use const FlixTech\AvroSerializer\Common\get;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_AVRO;
use const FlixTech\AvroSerializer\Protocol\PROTOCOL_ACCESSOR_SCHEMA_ID;
use const FlixTech\AvroSerializer\Protocol\WIRE_FORMAT_PROTOCOL_VERSION;
use const Widmogrod\Functional\identity;
use const Widmogrod\Functional\reThrow;

use function FlixTech\AvroSerializer\Common\memoize;
use function FlixTech\AvroSerializer\Protocol\decode;
use function FlixTech\AvroSerializer\Protocol\encoder;
use function FlixTech\AvroSerializer\Protocol\validator;
use function FlixTech\AvroSerializer\Serialize\avroDatumReader;
use function FlixTech\AvroSerializer\Serialize\avroDatumWriter;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\valueOf;

class RecordSerializer
{
    public const OPTION_REGISTER_MISSING_SCHEMAS = 'register_missing_schemas';
    public const OPTION_REGISTER_MISSING_SUBJECTS = 'register_missing_subjects';

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var callable
     */
    protected $datumWriterFactoryFunc;

    /**
     * @var callable
     */
    protected $datumReaderFactoryFunc;

    /**
     * @var callable
     */
    protected $protocolEncoderFactoryFunc;

    /**
     * @var callable
     */
    protected $schemaIdGetter;

    /**
     * @var callable
     */
    protected $avroBinaryGetter;

    /**
     * @var bool
     */
    protected $registerMissingSchemas;

    /**
     * @var bool
     */
    protected $registerNonExistingSubjects;

    /**
     * @var callable
     */
    protected $protocolValidatorFunc;

    /**
     * RecordSerializer constructor.
     *
     * @param array<string,mixed> $options
     *
     * @throws \AvroIOException
     */
    public function __construct(Registry $registry, array $options = [])
    {
        $this->registry = $registry;

        $this->datumWriterFactoryFunc = avroDatumWriter();
        $this->datumReaderFactoryFunc = avroDatumReader();

        $this->protocolEncoderFactoryFunc = encoder(WIRE_FORMAT_PROTOCOL_VERSION);
        $this->protocolValidatorFunc = validator(WIRE_FORMAT_PROTOCOL_VERSION);

        $get = curryN(2, get);

        $this->schemaIdGetter = $get(PROTOCOL_ACCESSOR_SCHEMA_ID);
        $this->avroBinaryGetter = $get(PROTOCOL_ACCESSOR_AVRO);

        $this->registerMissingSchemas = isset($options[self::OPTION_REGISTER_MISSING_SCHEMAS])
            ? (bool) $options[self::OPTION_REGISTER_MISSING_SCHEMAS]
            : false;

        $this->registerNonExistingSubjects = isset($options[self::OPTION_REGISTER_MISSING_SUBJECTS])
            ? (bool) $options[self::OPTION_REGISTER_MISSING_SUBJECTS]
            : false;
    }

    /**
     * @param mixed $record
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function encodeRecord(string $subject, AvroSchema $schema, $record): string
    {
        $schemaId = $this->getSchemaIdForSchema($subject, $schema);
        $cachedWriter = memoize($this->datumWriterFactoryFunc, [$schema], 'writer_' . $schemaId);

        return $cachedWriter($record)
            ->bind(memoize($this->protocolEncoderFactoryFunc, [$schemaId], 'encoder_' . $schemaId))
            ->either(reThrow, identity);
    }

    /**
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function decodeMessage(string $binaryMessage, AvroSchema $readersSchema = null)
    {
        $decoded = decode($binaryMessage);
        $schemaId = valueOf($decoded->bind($this->schemaIdGetter));
        $writersSchema = $this->extractValueFromRegistryResponse($this->registry->schemaForId($schemaId));
        $readersSchema = $readersSchema ?? $writersSchema;
        $cachedReader = memoize($this->datumReaderFactoryFunc, [$writersSchema], 'reader_' . $schemaId);

        /** @var \Widmogrod\Monad\Maybe\Maybe $validated */
        $validated = $decoded->bind($this->protocolValidatorFunc);

        /** @var \Widmogrod\Monad\Either\Either $read */
        $read = $validated
            ->orElse(static function () { throw new \InvalidArgumentException('Could not validate message wire protocol.'); })
            ->bind($this->avroBinaryGetter)
            ->bind($cachedReader($readersSchema));

        return $read->either(reThrow, identity);
    }

    /**
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    private function getSchemaIdForSchema(string $subject, AvroSchema $schema): int
    {
        try {
            $schemaId = $this->extractValueFromRegistryResponse($this->registry->schemaId($subject, $schema));
        } catch (SchemaRegistryException $e) {
            $this->handleSubjectOrSchemaNotFound($e);

            $schemaId = $this->extractValueFromRegistryResponse($this->registry->register($subject, $schema));
        }

        return $schemaId;
    }

    /**
     * @param PromiseInterface|\Exception|\Psr\Http\Message\ResponseInterface $response
     *
     * @return mixed
     *
     * @throws \Exception
     */
    private function extractValueFromRegistryResponse($response)
    {
        if ($response instanceof PromiseInterface) {
            $response = $response->wait();
        }

        if ($response instanceof \Exception) {
            throw $response;
        }

        return $response;
    }

    /**
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    private function handleSubjectOrSchemaNotFound(SchemaRegistryException $e): void
    {
        switch (\get_class($e)) {
            case SchemaNotFoundException::class:
                if (!$this->registerMissingSchemas) {
                    throw $e;
                }

                break;
            case SubjectNotFoundException::class:
                if (!$this->registerNonExistingSubjects) {
                    throw $e;
                }

                break;
            default:
                // @codeCoverageIgnoreStart
                throw $e;
            // @codeCoverageIgnoreEnd
        }
    }
}
