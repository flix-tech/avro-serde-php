<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Generation\Attributes;

enum Type: string
{
    case RECORD = 'record';
    case NULL = 'null';
    case BOOLEAN = 'boolean';
    case INT = 'int';
    case LONG = 'long';
    case FLOAT = 'float';
    case DOUBLE = 'double';
    case BYTES = 'bytes';
    case STRING = 'string';
    case ARRAY = 'array';
    case MAP = 'map';
    case ENUM = 'enum';
    case FIXED = 'fixed';
    case DATE = 'date';
    case DURATION = 'duration';
    case LOCAL_TIMESTAMP_MICROS = 'local-timestamp-micros';
    case LOCAL_TIMESTAMP_MILLIS = 'local-timestamp-millis';
    case TIME_MICROS = 'time-micros';
    case TIME_MILLIS = 'time-millis';
    case TIMESTAMP_MICROS = 'timestamp-micros';
    case TIMESTAMP_MILLIS = 'timestamp-millis';
    case UUID = 'uuid';
}
