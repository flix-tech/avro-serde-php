<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\SchemaResolvers;

use FlixTech\AvroSerializer\Objects\SchemaResolvers\FileResolver;
use PHPUnit\Framework\TestCase;

use function FlixTech\AvroSerializer\Common\inflectRecord;

class FileResolverTest extends TestCase
{
    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_find_value_and_key_schemas_when_defined(): void
    {
        $fileSchemaResolver = $this->getFileSchemaResolverInstance();

        $valueSchema = $fileSchemaResolver->valueSchemaFor(new TestRecordOne());
        $this->assertEquals(\AvroSchema::parse('{"type": "int"}'), $valueSchema);

        $keySchema = $fileSchemaResolver->keySchemaFor(new TestRecordOne());
        $this->assertEquals(\AvroSchema::parse('{"type": "string"}'), $keySchema);

        $valueSchema = $fileSchemaResolver->valueSchemaFor(new TestRecordTwo());
        $this->assertEquals(\AvroSchema::parse('{"type": "int"}'), $valueSchema);

        $this->assertNull($fileSchemaResolver->keySchemaFor(new TestRecordTwo()));
    }

    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_fail_for_non_existing_value_schema(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $fromSchemaFileResolver = $this->getFileSchemaResolverInstance();

        $this->assertNull($fromSchemaFileResolver->keySchemaFor(new TestRecordThree()));
        $fromSchemaFileResolver->valueSchemaFor(new TestRecordThree());
    }

    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_fail_for_value_schema_for_invalid_inflector_result(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $baseDir = __DIR__ . '/files';

        $inflector = static function () {
            return '';
        };

        $fromSchemaFileResolver = new FileResolver($baseDir, $inflector);
        $fromSchemaFileResolver->valueSchemaFor(new TestRecordOne());
    }

    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_fail_for_key_schema_for_invalid_inflector_result(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $baseDir = __DIR__ . '/files';

        $inflector = static function () {
            return '';
        };

        $fromSchemaFileResolver = new FileResolver($baseDir, $inflector);
        $fromSchemaFileResolver->keySchemaFor(new TestRecordOne());
    }

    protected function getFileSchemaResolverInstance(): FileResolver
    {
        $baseDir = __DIR__ . '/files';

        $inflector = static function ($record, bool $isKey) {
            $ext = $isKey ? '.key.json' : '.json';

            /** @var \Widmogrod\Common\ValueOfInterface $inflectedString */
            $inflectedString = inflectRecord($record)
                ->map(
                    static function ($inflectedObjectName) use ($ext) {
                        return $inflectedObjectName . $ext;
                    }
                );

            return $inflectedString->extract();
        };

        return new FileResolver($baseDir, $inflector);
    }
}

class TestRecordOne
{
}
class TestRecordTwo
{
}
class TestRecordThree
{
}
