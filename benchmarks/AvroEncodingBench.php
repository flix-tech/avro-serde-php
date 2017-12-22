<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Benchmarks;

use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException;
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

    public function setUp(): void
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
        try {
            $this->binaryMessage = $this->serializer->encodeRecord('test', $this->schema, self::TEST_RECORD);
        } catch (\Exception $e) {
        } catch (SchemaRegistryException $e) {
        }
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchEncode(): void
    {
        $this->serializer->encodeRecord('test', $this->schema, self::TEST_RECORD);
    }

    /**
     * @Revs(1000)
     * @Iterations(5)
     *
     * @throws \Exception
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function benchDecode(): void
    {
        $this->serializer->decodeMessage($this->binaryMessage);
    }
}
