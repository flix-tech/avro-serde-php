<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use FlixTech\AvroSerializer\Objects\Schema;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\ArraysWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\EmptyRecord;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\MapsWithComplexType;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\PrimitiveTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\RecordWithComplexTypes;
use FlixTech\AvroSerializer\Test\Objects\Schema\Generation\Fixture\RecordWithRecordType;
use PHPUnit\Framework\TestCase;

class SchemaGeneratorTest extends TestCase
{
    /**
     * @var Schema\Generation\SchemaGenerator
     */
    private $generator;

    protected function setUp(): void
    {
        AnnotationRegistry::registerLoader('class_exists');

        $this->generator = new Schema\Generation\SchemaGenerator(
            new Schema\Generation\AnnotationReader(
                new AnnotationReader()
            )
        );
    }

    /**
     * @test
     */
    public function it_should_generate_an_empty_record()
    {
        $schema = $this->generator->generate(EmptyRecord::class);

        $expected = Schema::record()
            ->name('EmptyRecord')
            ->namespace('org.acme');

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_generate_a_record_schema_with_primitive_types()
    {
        $schema = $this->generator->generate(PrimitiveTypes::class);

        $expected = Schema::record()
            ->name('PrimitiveTypes')
            ->namespace('org.acme')
            ->field(
                'nullType',
                Schema::null(),
                Schema\Record\FieldOption::doc('null type')
            )
            ->field(
                'isItTrue',
                Schema::boolean(),
                Schema\Record\FieldOption::default(false)
            )
            ->field(
                'intType',
                Schema::int()
            )
            ->field(
                'longType',
                Schema::long(),
                Schema\Record\FieldOption::orderAsc()
            )
            ->field(
                'floatType',
                Schema::float(),
                Schema\Record\FieldOption::aliases('foo', 'bar')
            )
            ->field(
                'doubleType',
                Schema::double()
            )
            ->field(
                'bytesType',
                Schema::bytes()
            )
            ->field(
                'stringType',
                Schema::string()
            );

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_generate_a_schema_record_with_complex_types()
    {
        $schema = $this->generator->generate(RecordWithComplexTypes::class);

        $expected = Schema::record()
            ->name('RecordWithComplexTypes')
            ->field(
                'array',
                Schema::array()
                    ->items(Schema::string())
                    ->default(['foo', 'bar'])
            )
            ->field(
                'map',
                Schema::map()
                    ->values(Schema::int())
                    ->default(['foo' => 42, 'bar' => 42])
            )
            ->field(
                'enum',
                Schema::enum()
                    ->name('Suit')
                    ->symbols('SPADES', 'HEARTS', 'DIAMONDS', 'CLUBS'),
                Schema\Record\FieldOrder::asc()
            )
            ->field(
                'fixed',
                Schema::fixed()
                    ->name('md5')
                    ->namespace('org.acme')
                    ->aliases('foo', 'bar')
                    ->size(16)
            )
            ->field(
                'union',
                Schema::union(Schema::string(), Schema::int(), Schema::array()->items(Schema::string()))
            );

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_generate_records_containing_records()
    {
        $schema = $this->generator->generate(RecordWithRecordType::class);

        $expected = Schema::record()
            ->name('RecordWithRecordType')
            ->field(
                'simpleField',
                Schema::record()
                    ->name('SimpleRecord')
                    ->namespace('org.acme')
                    ->doc('This a simple record for testing purposes')
                    ->field(
                        'intType',
                        Schema::int(),
                        Schema\Record\FieldOption::default(42)
                    ),
            )
            ->field(
                'unionField',
                Schema::union(
                    Schema::null(),
                    Schema::named('org.acme.SimpleRecord')
                )
            );

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_generate_a_record_schema_with_arrays_containing_complex_types()
    {
        $schema = $this->generator->generate(ArraysWithComplexType::class);

        $expected = Schema::record()
            ->name('ArraysWithComplexType')
            ->field(
                'arrayWithUnion',
                Schema::array()
                    ->items(
                        Schema::union(
                            Schema::string(),
                            Schema::array()->items(Schema::string())
                        )
                    )
            )
            ->field(
                'arrayWithMap',
                Schema::array()
                    ->items(
                        Schema::map()->values(Schema::string())
                    )
            );

        $this->assertEquals($expected, $schema);
    }

    /**
     * @test
     */
    public function it_should_generate_a_record_schema_with_maps_containing_complex_types()
    {
        $schema = $this->generator->generate(MapsWithComplexType::class);

        $expected = Schema::record()
            ->name('MapsWithComplexType')
            ->field(
                'mapWithUnion',
                Schema::map()
                    ->values(
                        Schema::union(
                            Schema::string(),
                            Schema::array()->items(Schema::string())
                        )
                    )
            )
            ->field(
                'mapWithArray',
                Schema::map()
                    ->values(
                        Schema::array()->items(Schema::string())
                    )
            );

        $this->assertEquals($expected, $schema);
    }
}
