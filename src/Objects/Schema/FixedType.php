<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class FixedType extends ComplexType
{
    public function __construct()
    {
        parent::__construct('fixed');
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute('namespace', $namespace);
    }

    public function name(string $name): self
    {
        return $this->attribute('name', $name);
    }

    public function size(int $size): self
    {
        return $this->attribute('size', $size);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute('aliases', $aliases);
    }
}
