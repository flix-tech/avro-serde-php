<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Benchmarks;

use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
use FlixTech\SchemaRegistryApi\Registry;
use FlixTech\SchemaRegistryApi\Registry\BlockingRegistry;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * @BeforeMethods({"setUp"})
 */
class AvroEncodingBench
{
    public const ASYNC = 'async';
    public const ASYNC_CACHED = 'async_cached';
    public const SYNC = 'sync';
    public const SYNC_CACHED = 'sync_cached';

    public const TEST_MODES = [
        self::ASYNC,
        self::ASYNC_CACHED,
        self::SYNC,
        self::SYNC_CACHED,
    ];

    public const TEST_RECORD = [
        'name' => 'Thomas',
        'age' => 36,
    ];

    public const SCHEMA_JSON = /** @lang JSON */
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

    /**
     * @var \FlixTech\AvroSerializer\Objects\RecordSerializer[]
     */
    private $serializers = [];

    /**
     * @var string[]
     */
    private $messages = [];

    /**
     * @var \AvroSchema
     */
    private $schema;

    /**
     * @throws \AvroSchemaParseException
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function setUp(): void
    {
        $this->schema = \AvroSchema::parse(self::SCHEMA_JSON);

        $this->prepareTestForMode(self::ASYNC, new PromisingRegistry(
            new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
        ));

        $this->prepareTestForMode(self::SYNC, new BlockingRegistry(
            new PromisingRegistry(
                new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
            )
        ));

        $this->prepareTestForMode(self::ASYNC_CACHED, new CachedRegistry(
            new PromisingRegistry(
                new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
            ),
            new AvroObjectCacheAdapter()
        ));

        $this->prepareTestForMode(self::SYNC_CACHED, new CachedRegistry(
            new BlockingRegistry(
                new PromisingRegistry(
                    new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
                )
            ),
            new AvroObjectCacheAdapter()
        ));
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchEncodeWithSyncRegistry(): void
    {
        $this->serializers[self::SYNC]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchDecodeWithSyncRegistry(): void
    {
        $this->serializers[self::SYNC]->decodeMessage($this->messages[self::SYNC]);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchEncodeWithAsyncRegistry(): void
    {
        $this->serializers[self::ASYNC]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchDecodeWithAsyncRegistry(): void
    {
        $this->serializers[self::ASYNC]->decodeMessage($this->messages[self::ASYNC]);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchEncodeWithAsyncCachedRegistry(): void
    {
        $this->serializers[self::ASYNC_CACHED]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchDecodeWithAsyncCachedRegistry(): void
    {
        $this->serializers[self::ASYNC_CACHED]->decodeMessage($this->messages[self::ASYNC_CACHED]);
    }


    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchEncodeWithSyncCachedRegistry(): void
    {
        $this->serializers[self::SYNC_CACHED]->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchDecodeWithSyncCachedRegistry(): void
    {
        $this->serializers[self::SYNC_CACHED]->decodeMessage($this->messages[self::SYNC_CACHED]);
    }

    /**
     * @param string                               $mode
     * @param \FlixTech\SchemaRegistryApi\Registry $registry
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    private function prepareTestForMode(string $mode, Registry $registry): void
    {
        $result = $registry->register('test', $this->schema);
        !$result instanceof PromiseInterface ?: $result->wait();

        $this->serializers[$mode] = new RecordSerializer($registry);

        try {
            $this->messages[$mode] = $this->serializers[$mode]->encodeRecord(
                'test',
                $this->schema,
                self::TEST_RECORD
            );
        } catch (\Exception $e) {
        } catch (SchemaRegistryException $e) {
        }
    }
}
