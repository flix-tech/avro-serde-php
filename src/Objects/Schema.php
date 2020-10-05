<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use AvroSchema;
use FlixTech\AvroSerializer\Objects\Schema\ArrayType;
use FlixTech\AvroSerializer\Objects\Schema\BooleanType;
use FlixTech\AvroSerializer\Objects\Schema\BytesType;
use FlixTech\AvroSerializer\Objects\Schema\DoubleType;
use FlixTech\AvroSerializer\Objects\Schema\EnumType;
use FlixTech\AvroSerializer\Objects\Schema\FixedType;
use FlixTech\AvroSerializer\Objects\Schema\FloatType;
use FlixTech\AvroSerializer\Objects\Schema\IntType;
use FlixTech\AvroSerializer\Objects\Schema\LongType;
use FlixTech\AvroSerializer\Objects\Schema\MapType;
use FlixTech\AvroSerializer\Objects\Schema\NullType;
use FlixTech\AvroSerializer\Objects\Schema\RecordType;
use FlixTech\AvroSerializer\Objects\Schema\StringType;
use FlixTech\AvroSerializer\Objects\Schema\UnionType;

abstract class Schema implements Definition
{
    public static function null(): NullType
    {
        return new NullType();
    }

    public static function boolean(): BooleanType
    {
        return new BooleanType();
    }

    public static function int(): IntType
    {
        return new IntType();
    }

    public static function long(): LongType
    {
        return new LongType();
    }

    public static function float(): FloatType
    {
        return new FloatType();
    }

    public static function double(): DoubleType
    {
        return new DoubleType();
    }

    public static function bytes(): BytesType
    {
        return new BytesType();
    }

    public static function string(): StringType
    {
        return new StringType();
    }

    public static function record(): RecordType
    {
        return new RecordType();
    }

    public static function enum(): EnumType
    {
        return new EnumType();
    }

    public static function array(): ArrayType
    {
        return new ArrayType();
    }

    public static function map(): MapType
    {
        return new MapType();
    }

    public static function union(Schema $type, Schema ...$types): UnionType
    {
        return new UnionType($type, ...$types);
    }

    public static function fixed(): FixedType
    {
        return new FixedType();
    }

    public function parse(): AvroSchema
    {
        $avro = $this->serialize();

        return AvroSchema::real_parse($avro);
    }
}
