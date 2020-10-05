<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema\Record;

abstract class FieldOption
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(string $name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public static function doc(string $doc): FieldDoc
    {
        return new FieldDoc($doc);
    }

    /**
     * @param mixed $default
     */
    public static function default($default): FieldDefault
    {
        return new FieldDefault($default);
    }

    public static function orderAsc(): FieldOrder
    {
        return FieldOrder::asc();
    }

    public static function orderDesc(): FieldOrder
    {
        return FieldOrder::desc();
    }

    public static function orderIgnore(): FieldOrder
    {
        return FieldOrder::ignore();
    }

    public static function aliases(string $alias, string ...$other): FieldAliases
    {
        return new FieldAliases($alias, ...$other);
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
