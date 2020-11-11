<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects;

use AvroSchema;
use FlixTech\AvroSerializer\Objects\Schema\ArrayType;
use FlixTech\AvroSerializer\Objects\Schema\BooleanType;
use FlixTech\AvroSerializer\Objects\Schema\BytesType;
use FlixTech\AvroSerializer\Objects\Schema\DateType;
use FlixTech\AvroSerializer\Objects\Schema\DoubleType;
use FlixTech\AvroSerializer\Objects\Schema\DurationType;
use FlixTech\AvroSerializer\Objects\Schema\EnumType;
use FlixTech\AvroSerializer\Objects\Schema\FixedType;
use FlixTech\AvroSerializer\Objects\Schema\FloatType;
use FlixTech\AvroSerializer\Objects\Schema\IntType;
use FlixTech\AvroSerializer\Objects\Schema\LocalTimestampMicros;
use FlixTech\AvroSerializer\Objects\Schema\LocalTimestampMillisType;
use FlixTech\AvroSerializer\Objects\Schema\LongType;
use FlixTech\AvroSerializer\Objects\Schema\MapType;
use FlixTech\AvroSerializer\Objects\Schema\NamedType;
use FlixTech\AvroSerializer\Objects\Schema\NullType;
use FlixTech\AvroSerializer\Objects\Schema\RecordType;
use FlixTech\AvroSerializer\Objects\Schema\StringType;
use FlixTech\AvroSerializer\Objects\Schema\TimeMicrosType;
use FlixTech\AvroSerializer\Objects\Schema\TimeMillisType;
use FlixTech\AvroSerializer\Objects\Schema\TimestampMicrosType;
use FlixTech\AvroSerializer\Objects\Schema\TimestampMillisType;
use FlixTech\AvroSerializer\Objects\Schema\UnionType;
use FlixTech\AvroSerializer\Objects\Schema\UuidType;

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

    public static function named(string $name): NamedType
    {
        return new NamedType($name);
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

    public static function uuid(): UuidType
    {
        return new UuidType();
    }

    public static function date(): DateType
    {
        return new DateType();
    }

    public static function timeMillis(): TimeMillisType
    {
        return new TimeMillisType();
    }

    public static function timeMicros(): TimeMicrosType
    {
        return new TimeMicrosType();
    }

    public static function timestampMillis(): TimestampMillisType
    {
        return new TimestampMillisType();
    }

    public static function timestampMicros(): TimestampMicrosType
    {
        return new TimestampMicrosType();
    }

    public static function localTimestampMillis(): LocalTimestampMillisType
    {
        return new LocalTimestampMillisType();
    }

    public static function localTimestampMicros(): LocalTimestampMicros
    {
        return new LocalTimestampMicros();
    }

    public static function duration(): DurationType
    {
        return new DurationType();
    }

    final public function parse(): AvroSchema
    {
        $avro = $this->serialize();

        return AvroSchema::real_parse($avro);
    }
}
