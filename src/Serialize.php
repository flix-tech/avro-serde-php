<?php

namespace FlixTech\AvroSerializer\Serialize;

use AvroIOBinaryDecoder;
use AvroIOBinaryEncoder;
use AvroIODatumReader;
use AvroIODatumWriter;
use AvroSchema;
use AvroStringIO;
use FlixTech\AvroSerializer\Objects\Exceptions\Exceptions;
use Widmogrod\Monad\Either\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\tryCatch;

const avroStringIo = '\FlixTech\AvroSerializer\Serialize\avroStringIo';

/**
 * @throws \AvroIOException
 */
function avroStringIo(string $contents): AvroStringIO
{
    return new AvroStringIO($contents);
}

const avroBinaryEncoder = '\FlixTech\AvroSerializer\Serialize\avroBinaryEncoder';

function avroBinaryEncoder(AvroStringIO $io): AvroIOBinaryEncoder
{
    return new AvroIOBinaryEncoder($io);
}

const avroBinaryDecoder = '\FlixTech\AvroSerializer\Serialize\avroBinaryDecoder';

function avroBinaryDecoder(AvroStringIO $io): AvroIOBinaryDecoder
{
    return new AvroIOBinaryDecoder($io);
}

const avroDatumWriter = '\FlixTech\AvroSerializer\Serialize\avroDatumWriter';

/**
 * @throws \AvroIOException
 */
function avroDatumWriter(): callable
{
    $writer = new AvroIODatumWriter();
    $io = avroStringIo('');

    return curryN(4, writeDatum)($writer)($io);
}

const writeDatum = '\FlixTech\AvroSerializer\Serialize\writeDatum';

/**
 * @param mixed $record
 */
function writeDatum(AvroIODatumWriter $writer, AvroStringIO $io, AvroSchema $schema, $record): Either
{
    return tryCatch(
        static function ($record) use ($schema, $writer, $io) {
            $io->truncate();
            $writer->write_data($schema, $record, avroBinaryEncoder($io));

            return Right::of($io->string());
        },
        static function (\AvroException $e) use ($record, $schema) {
            return Left::of(
                Exceptions::forEncode($record, $schema, $e)
            );
        },
        $record
    );
}

const avroDatumReader = '\FlixTech\AvroSerializer\Serialize\avroDatumReader';

/**
 * @throws \AvroIOException
 */
function avroDatumReader(): callable
{
    $reader = new AvroIODatumReader();
    $io = avroStringIo('');

    return curryN(5, readDatum)($reader)($io);
}

const readDatum = '\FlixTech\AvroSerializer\Serialize\readDatum';

/**
 * @param mixed $data
 */
function readDatum(
    AvroIODatumReader $reader,
    AvroStringIO $io,
    AvroSchema $writersSchema,
    AvroSchema $readersSchema,
    $data
): Either {
    return tryCatch(
        static function ($data) use ($writersSchema, $readersSchema, $reader, $io) {
            $io->truncate();
            $io->write($data);
            $io->seek(0);

            return Right::of($reader->read_data($writersSchema, $readersSchema, avroBinaryDecoder($io)));
        },
        static function (\AvroException $e) use ($data) {
            return Left::of(
                Exceptions::forDecode($data, $e)
            );
        },
        $data
    );
}
