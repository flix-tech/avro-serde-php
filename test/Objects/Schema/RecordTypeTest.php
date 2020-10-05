<?php

namespace FlixTech\AvroSerializer\Test\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use FlixTech\AvroSerializer\Objects\Schema\Record\FieldOption;
use PHPUnit\Framework\TestCase;

class RecordTypeTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_serialize_record_types(): void
    {
        $serializedRecord = Schema::record()
            ->name('object')
            ->namespace('org.acme')
            ->doc('A test object')
            ->aliases(['stdClass', 'array'])
            ->field('name', Schema::string(), FieldOption::doc('Name of the object'), FieldOption::orderDesc())
            ->field('answer', Schema::int(), FieldOption::default(42), FieldOption::orderAsc(), FieldOption::aliases('wrong', 'correct'))
            ->field('ignore', Schema::boolean(), FieldOption::orderIgnore())
            ->serialize();

        $expectedRecord = [
            'type' => 'record',
            'name' => 'object',
            'namespace' => 'org.acme',
            'doc' => 'A test object',
            'aliases' => ['stdClass', 'array'],
            'fields' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'doc' => 'Name of the object',
                    'order' => 'descending',
                ],
                [
                    'name' => 'answer',
                    'type' => 'int',
                    'default' => 42,
                    'order' => 'ascending',
                    'aliases' => ['wrong', 'correct'],
                ],
                [
                    'name' => 'ignore',
                    'type' => 'boolean',
                    'order' => 'ignore',
                ],
            ],
        ];

        $this->assertEquals($expectedRecord, $serializedRecord);
    }

    /**
     * @test
     */
    public function it_should_parse_record_types(): void
    {
        $parsedSchema = Schema::record()
            ->name('object')
            ->namespace('org.acme')
            ->doc('A test object')
            ->aliases(['stdClass', 'array'])
            ->field('name', Schema::string(), FieldOption::doc('Name of the object'), FieldOption::orderDesc())
            ->field('answer', Schema::int(), FieldOption::default(42), FieldOption::orderAsc(), FieldOption::aliases('wrong', 'correct'))
            ->field('ignore', Schema::boolean(), FieldOption::orderIgnore())
            ->parse();

        $this->assertInstanceOf(\AvroSchema::class, $parsedSchema);
        $this->assertEquals('record', $parsedSchema->type());
    }
}
