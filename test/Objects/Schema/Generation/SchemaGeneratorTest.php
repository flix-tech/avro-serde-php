<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\Schema\Generation;

use FlixTech\AvroSerializer\Objects\Schema\Generation\SchemaGenerator;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AttributeReader;
use PHPUnit\Framework\Attributes\Test;
use FlixTech\AvroSerializer\Objects\Schema\Record\FieldOption;
use FlixTech\AvroSerializer\Objects\Schema\Record\FieldOrder;
use Doctrine\Common\Annotations\AnnotationReader;
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
    private ?SchemaGenerator $generatorDoctrineAnnotations;

    private ?SchemaGenerator $generatorAttributes;

    protected function setUp(): void
    {
        $this->generatorDoctrineAnnotations = new SchemaGenerator(
            new Schema\Generation\AnnotationReader(
                new AnnotationReader()
            )
        );

        $this->generatorAttributes = new SchemaGenerator(
            new AttributeReader()
        );
    }

    #[Test]
    public function it_should_generate_an_empty_record()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(EmptyRecord::class);

        $expected = Schema::record()
            ->name('EmptyRecord')
            ->namespace('org.acme');

        $this->assertEquals($expected, $schema);
    }

    #[Test]
    public function it_should_generate_a_record_schema_with_primitive_types()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(PrimitiveTypes::class);

        $expected = Schema::record()
            ->name('PrimitiveTypes')
            ->namespace('org.acme')
            ->field(
                'nullType',
                Schema::null(),
                FieldOption::doc('null type')
            )
            ->field(
                'isItTrue',
                Schema::boolean(),
                FieldOption::default(false)
            )
            ->field(
                'intType',
                Schema::int()
            )
            ->field(
                'longType',
                Schema::long(),
                FieldOption::orderAsc()
            )
            ->field(
                'floatType',
                Schema::float(),
                FieldOption::aliases('foo', 'bar')
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

    #[Test]
    public function it_should_generate_a_schema_record_with_complex_types()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(RecordWithComplexTypes::class);

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
                FieldOrder::asc()
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

    #[Test]
    public function it_should_generate_records_containing_records()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(RecordWithRecordType::class);

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
                        FieldOption::default(42)
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

    public function test_it_should_generate_records_containing_records_using_attributes(): void
    {
        $schema = $this->generatorAttributes->generate(RecordWithRecordType::class);

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
                        FieldOption::default(42)
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

    #[Test]
    public function it_should_generate_a_record_schema_with_arrays_containing_complex_types()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(ArraysWithComplexType::class);

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

    #[Test]
    public function it_should_generate_a_record_schema_with_maps_containing_complex_types()
    {
        $schema = $this->generatorDoctrineAnnotations->generate(MapsWithComplexType::class);

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
