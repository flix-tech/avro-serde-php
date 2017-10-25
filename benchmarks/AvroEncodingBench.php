<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Benchmarks;

use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\SchemaRegistryApi\Registry\Cache\AvroObjectCacheAdapter;
use FlixTech\SchemaRegistryApi\Registry\CachedRegistry;
use FlixTech\SchemaRegistryApi\Registry\PromisingRegistry;
use GuzzleHttp\Client;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

/**
 * @BeforeMethods({"setUp"})
 */
class AvroEncodingBench
{
    const TEST_RECORD = [
        'name' => 'Thomas',
        'age' => 36,
    ];

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

    /**
     * @var \FlixTech\AvroSerializer\Objects\RecordSerializer
     */
    private $serializer;

    /**
     * @var \AvroSchema
     */
    private $schema;

    /**
     * @var \FlixTech\SchemaRegistryApi\Registry
     */
    private $registry;

    /**
     * @var string
     */
    private $binaryMessage;

    public function setUp()
    {
        $this->registry = new CachedRegistry(
            new PromisingRegistry(
                new Client(['base_uri' => getenv('SCHEMA_REGISTRY_HOST')])
            ),
            new AvroObjectCacheAdapter()
        );

        $this->schema = \AvroSchema::parse(self::SCHEMA_JSON);
        $this->registry->register('test', $this->schema)->wait();

        $this->serializer = new RecordSerializer($this->registry);
        $this->binaryMessage = $this->serializer->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchEncode()
    {
        $this->serializer->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchDecode()
    {
        $this->serializer->decodeMessage($this->binaryMessage);
    }
}
