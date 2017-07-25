<?php

namespace FlixTech\AvroSerializer\Protocol;

use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;
use Widmogrod\Monad\IO;
use Widmogrod\Primitive\Num;
use Widmogrod\Primitive\Stringg;

use function Widmogrod\Functional\curry;
use function Widmogrod\Functional\tryCatch;
use function Widmogrod\Functional\valueOf;
use function Widmogrod\Monad\Control\doo;
use function Widmogrod\Monad\Control\runWith;
use function Widmogrod\Monad\IO\getLine;
use function Widmogrod\Monad\IO\putStrLn;

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

function encode(int $schemaId, string $binaryString): Either
{
    return tryCatch(
        function (string $binaryString) use ($schemaId) {
            return Right::of(pack('CNA*', version(), $schemaId, $binaryString));
        },
        Left::of,
        $binaryString
    );
}


const encoder = '\FlixTech\AvroSerializer\Protocol\encoder';

function encoder(): callable
{
    return curry(encode);
}


const decode = '\FlixTech\AvroSerializer\Protocol\decode';

function decode(string $binaryString): Either
{
    return tryCatch(
        function (string $binaryString): Either {
            $unpacked = unpack(
                sprintf(
                    'C%s/N%s/A*%s',
                    PROTOCOL_ACCESSOR_VERSION,
                    PROTOCOL_ACCESSOR_SCHEMA_ID,
                    PROTOCOL_ACCESSOR_AVRO
                ),
                $binaryString
            );

            return Right::of($unpacked);
        },
        Left::of,
        $binaryString
    );
}


const decoder = '\FlixTech\AvroSerializer\Protocol\decoder';

function decoder(): callable
{
    return curry(decode);
}

const encodeIO = '\FlixTech\AvroSerializer\Protocol\encodeIO';

function encodeIO(): IO
{
    return doo([
        putStrLn('Please provide the schema ID to be encoded:'),
        '$schemaId'
            => getLine()
                ->bind('\intval')
                ->bind(Num::of)
        ,
        putStrLn('Please provide the string to be encoded:'),
        '$binary'
            => getLine()
                ->bind(Stringg::of)
        ,
        runWith(
            function ($schemaId, $binary) {
                return putStrLn(valueOf(encoder()($schemaId)($binary)));
            },
            ['$schemaId', '$binary']
        )
    ]);
}
