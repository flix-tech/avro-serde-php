<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Integrations\Symfony\Serializer;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\AvroSerDeEncoder;
use FlixTech\AvroSerializer\Integrations\Symfony\Serializer\NameConverter\AvroNameConverter;
use FlixTech\AvroSerializer\Objects\Schema\Generation\AnnotationReader;
use FlixTech\AvroSerializer\Test\Integrations\Symfony\Serializer\Fixture\SampleUserRecord;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\NameConverter\AdvancedNameConverterInterface;

class AvroNameConverterTest extends TestCase
{
    /**
     * @var AvroNameConverter
     */
    private $nameConverter;

    protected function setUp(): void
    {
        if (!\interface_exists(AdvancedNameConverterInterface::class)) {
            $this->markTestSkipped('The advanced name converter is supported only in symfony 4 and forward');
        }

        $this->nameConverter = new AvroNameConverter(
            new AnnotationReader(new DoctrineAnnotationReader())
        );
    }

    /**
     * @test
     */
    public function it_should_return_the_normalized_property_name(): void
    {
        $normalizedName = $this->nameConverter
            ->normalize('name', SampleUserRecord::class, AvroSerDeEncoder::FORMAT_AVRO);
        $this->assertEquals('Name', $normalizedName);
    }

    /**
     * @test
     */
    public function it_should_return_the_denormalized_property_name(): void
    {
        $normalizedName = $this->nameConverter
            ->denormalize('Name', SampleUserRecord::class, AvroSerDeEncoder::FORMAT_AVRO);
        $this->assertEquals('name', $normalizedName);
    }
}
