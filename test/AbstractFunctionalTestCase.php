<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test;

use AvroSchema;
use PHPUnit\Framework\TestCase;

abstract class AbstractFunctionalTestCase extends TestCase
{
    public const HEX_BIN = '000000270f0c54686f6d617348';
    public const SCHEMA_ID = 9999;
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
    public const READERS_SCHEMA_JSON = /** @lang JSON */
        <<<JSON
{
  "type": "record",
  "name": "user",
  "fields": [
    {"name": "age", "type": "int"}
  ]
}
JSON;
    public const INVALID_READERS_SCHEMA_JSON = /** @lang JSON */
        <<<JSON
{"type": "enum", "name": "Foo", "symbols": ["A", "B", "C", "D"] }
JSON;
    public const INVALID_TEST_RECORD = [
        'first_name' => 'Thomas',
        'birth_date' => '1980-10-17',
    ];
    public const VALID_PROTOCOL_HEX_BIN = '000000270f3030303030303237306630633534363836663664363137333438';
    public const TEST_RECORD = [
        'name' => 'Thomas',
        'age' => 36,
    ];
    public const READERS_TEST_RECORD = [
        'age' => 36,
    ];
    public const INVALID_BIN_TOO_SHORT = '22';
    public const AVRO_ENCODED_RECORD_HEX_BIN = '0c54686f6d617348';
    public const INVALID_AVRO_ENCODED_RECORD_HEX_BIN = '0c54615f6d608348';
    public const INVALID_BIN_WRONG_VERSION = '44686f6d617348';

    /**
     * @var AvroSchema
     */
    protected $avroSchema;

    /**
     * @var AvroSchema
     */
    protected $readersSchema;

    /**
     * @var AvroSchema
     */
    protected $invalidSchema;

    /**
     * @throws \AvroSchemaParseException
     */
    protected function setUp(): void
    {
        $this->avroSchema = AvroSchema::parse(self::SCHEMA_JSON);
        $this->readersSchema = AvroSchema::parse(self::READERS_SCHEMA_JSON);
        $this->invalidSchema = AvroSchema::parse(self::INVALID_READERS_SCHEMA_JSON);
    }
}
