<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Exceptions;

use FlixTech\AvroSerializer\Objects\AvroSerializerException;
use RuntimeException;

class AvroEncodingException extends RuntimeException implements AvroSerializerException
{
}
