<?php

namespace FlixTech\AvroSerializer\Common;

use Widmogrod\Monad\Maybe\Maybe;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

const get = '\FlixTech\AvroSerializer\Common\get';

function get($key, array $array): Maybe
{
    return isset($array[$key])
        ? just($array[$key])
        : nothing();
}


const inflectRecord = '\FlixTech\AvroSerializer\Common\inflectRecord';

function inflectRecord($record): Maybe
{
    return is_object($record)
        ? just(str_replace('\\', '.', get_class($record)))
        : nothing();
}
