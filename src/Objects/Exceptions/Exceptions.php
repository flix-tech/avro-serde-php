<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Exceptions;

final class Exceptions
{
    public const ERROR_ENCODING = 501;
    public const ERROR_DECODING = 502;

    /**
     * @param mixed $record
     */
    public static function forEncode($record, \AvroSchema $schema, \Exception $previous = null): AvroEncodingException
    {
        $exportedRecord = \var_export($record, true);

        $message = <<<MESSAGE
Could not encode record with given Schema.

Record:
"$exportedRecord"

Schema:
"{(string) $schema}"
MESSAGE;

        return new AvroEncodingException($message, self::ERROR_ENCODING, $previous);
    }

    public static function forDecode(string $binaryMessage, \Exception $previous = null): AvroDecodingException
    {
        $convertedMessage = \bin2hex($binaryMessage);
        $message = <<<MESSAGE
Could not decode message.

Binary Message (as Hex):
"$convertedMessage"
MESSAGE;

        return new AvroDecodingException($message, self::ERROR_DECODING, $previous);
    }
}
