<?php

namespace FlixTech\AvroSerializer\Protocol;

use FlixTech\AvroSerializer\Objects\Exceptions\AvroDecodingException;
use FlixTech\AvroSerializer\Objects\Exceptions\AvroEncodingException;
use FlixTech\AvroSerializer\Objects\Exceptions\Exceptions;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use Widmogrod\Monad\Maybe\Maybe;

use function Widmogrod\Functional\curryN;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

const WIRE_FORMAT_PROTOCOL_VERSION = 0;
const PROTOCOL_ACCESSOR_VERSION = 'version';
const PROTOCOL_ACCESSOR_SCHEMA_ID = 'schemaId';
const PROTOCOL_ACCESSOR_AVRO = 'avro';

const version = '\FlixTech\AvroSerializer\Protocol\version';

function version(): int
{
    return WIRE_FORMAT_PROTOCOL_VERSION;
}

const encode = '\FlixTech\AvroSerializer\Protocol\encode';

function encode(int $protocolVersion, int $schemaId, string $avroEncodedBinaryString): Either
{
    /** @var bool|string $packed */
    $packed = @\pack('CNa*', $protocolVersion, $schemaId, $avroEncodedBinaryString);

    return false !== $packed
        ? Right::of($packed)
        // @codeCoverageIgnoreStart
        : Left::of(
            new AvroEncodingException(
                \sprintf(
                    'Could not pack message with format "CNa*", protocol version "%d" and schema id "%d"',
                    $protocolVersion,
                    $schemaId
                )
            )
        ); // @codeCoverageIgnoreEnd
}

const encoder = '\FlixTech\AvroSerializer\Protocol\encoder';

function encoder(int $protocolVersion): callable
{
    return curryN(3, encode)($protocolVersion);
}

const decode = '\FlixTech\AvroSerializer\Protocol\decode';

function decode(string $binaryString): Either
{
    $packedFormat = \sprintf(
        'C%s/N%s/a*%s',
        PROTOCOL_ACCESSOR_VERSION,
        PROTOCOL_ACCESSOR_SCHEMA_ID,
        PROTOCOL_ACCESSOR_AVRO
    );

    /** @var array<mixed,mixed>|bool $unpacked */
    $unpacked = @\unpack(
        $packedFormat,
        $binaryString
    );

    return false !== $unpacked
        ? Right::of($unpacked)
        : Left::of(
            new AvroDecodingException(
                \sprintf('Could not decode message with packed format "%s"',
                    $packedFormat
                ),
                Exceptions::ERROR_DECODING
            )
        );
}

const validate = '\FlixTech\AvroSerializer\Protocol\validate';

/**
 * @param array<mixed,mixed> $decoded
 */
function validate(int $protocolVersion, array $decoded): Maybe
{
    $valid = isset($decoded[PROTOCOL_ACCESSOR_VERSION], $decoded[PROTOCOL_ACCESSOR_SCHEMA_ID], $decoded[PROTOCOL_ACCESSOR_AVRO])
        && $decoded[PROTOCOL_ACCESSOR_VERSION] === $protocolVersion
        && \is_int($decoded[PROTOCOL_ACCESSOR_SCHEMA_ID])
        && \is_string($decoded[PROTOCOL_ACCESSOR_AVRO]);

    return $valid
        ? just($decoded)
        : nothing();
}

const validator = '\FlixTech\AvroSerializer\Protocol\validator';

/**
 * @return \Closure
 */
function validator(int $protocolVersion)
{
    return curryN(2, validate)($protocolVersion);
}
