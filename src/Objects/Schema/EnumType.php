<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class EnumType extends ComplexType
{
    public function __construct()
    {
        parent::__construct('enum');
    }

    public function name(string $name): self
    {
        return $this->attribute('name', $name);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute('namespace', $namespace);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute('aliases', $aliases);
    }

    public function doc(string $doc): self
    {
        return $this->attribute('doc', $doc);
    }

    public function symbols(string $symbol, string ...$symbols): self
    {
        \array_unshift($symbols, $symbol);

        return $this->attribute('symbols', $symbols);
    }

    public function default(string $default): self
    {
        return $this->attribute('default', $default);
    }
}
