<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects;

use FlixTech\AvroSerializer\Objects\DefaultRecordSerializerFactory;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use FlixTech\AvroSerializer\Test\AbstractFunctionalTestCase;
use FlixTech\SchemaRegistryApi\Exception\IncompatibleAvroSchemaException;

/**
 * @group integration
 */
class RecordSerializerIntegrationTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function it_encodes_valid_records(): RecordSerializer
    {
        $serializer = DefaultRecordSerializerFactory::get(\getenv('SCHEMA_REGISTRY_HOST'));
        $encoded = $serializer->encodeRecord('test-value', $this->avroSchema, self::TEST_RECORD);
        $decoded = $serializer->decodeMessage($encoded);

        $this->assertEquals(self::TEST_RECORD, $decoded);

        return $serializer;
    }

    /**
     * @test
     *
     * @depends it_encodes_valid_records
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function it_cannot_evolve_incompatible_schema(RecordSerializer $serializer): void
    {
        $this->expectException(IncompatibleAvroSchemaException::class);
        $serializer->encodeRecord('test-value', $this->invalidSchema, self::TEST_RECORD);
    }

    /**
     * @test
     *
     * @depends it_encodes_valid_records
     *
     * @throws \FlixTech\SchemaRegistryApi\Exception\SchemaRegistryException
     */
    public function it_decodes_with_readers_schema(RecordSerializer $serializer): RecordSerializer
    {
        $encoded = $serializer->encodeRecord('test-value', $this->avroSchema, self::TEST_RECORD);
        $decoded = $serializer->decodeMessage($encoded, $this->readersSchema);

        $this->assertEquals(self::READERS_TEST_RECORD, $decoded);

        return $serializer;
    }
}
