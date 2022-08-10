<?php

namespace FlixTech\AvroSerializer\Common;

use Widmogrod\Monad\Maybe\Maybe;

use function Widmogrod\Functional\curryN;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

const get = '\FlixTech\AvroSerializer\Common\get';

/**
 * @param mixed              $key
 * @param array<mixed,mixed> $array
 */
function get($key, array $array): Maybe
{
    return isset($array[$key])
        ? just($array[$key])
        : nothing();
}

const getter = '\FlixTech\AvroSerializer\Common\getter';

function getter(): callable
{
    return curryN(2, get);
}

const inflectRecord = '\FlixTech\AvroSerializer\Common\inflectRecord';

/**
 * @param mixed $record
 */
function inflectRecord($record): Maybe
{
    return \is_object($record)
        ? just(\str_replace('\\', '.', \get_class($record)))
        : nothing();
}

const memoize = '\FlixTech\AvroSerializer\Common\memoize';

/**
 * @param array<mixed,mixed> $arguments
 * @param string|null        $key
 *
 * @return mixed|null
 */
function memoize(callable $callback = null, array $arguments = [], $key = null)
{
    static $storage = [];

    if (null === $callback) {
        $storage = [];

        return null;
    }

    $key = $key ?? \md5(\serialize($arguments));

    if (!\array_key_exists($key, $storage) && !isset($storage[$key])) {
        $storage[$key] = $callback(...$arguments);
    }

    return $storage[$key];
}
