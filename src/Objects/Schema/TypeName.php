<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class TypeName
{
    public const RECORD = 'record';
    public const NULL = 'null';
    public const BOOLEAN = 'boolean';
    public const INT = 'int';
    public const LONG = 'long';
    public const FLOAT = 'float';
    public const DOUBLE = 'double';
    public const BYTES = 'bytes';
    public const STRING = 'string';
    public const ARRAY = 'array';
    public const MAP = 'map';
    public const ENUM = 'enum';
    public const FIXED = 'fixed';
    public const DATE = 'date';
    public const DURATION = 'duration';
    public const LOCAL_TIMESTAMP_MICROS = 'local-timestamp-micros';
    public const LOCAL_TIMESTAMP_MILLIS = 'local-timestamp-millis';
    public const TIME_MICROS = 'time-micros';
    public const TIME_MILLIS = 'time-millis';
    public const TIMESTAMP_MICROS = 'timestamp-micros';
    public const TIMESTAMP_MILLIS = 'timestamp-millis';
    public const UUID = 'uuid';
}
