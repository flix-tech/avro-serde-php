<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class FixedType extends ComplexType
{
    public function __construct()
    {
        parent::__construct(TypeName::FIXED);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute(AttributeName::NAMESPACE, $namespace);
    }

    public function name(string $name): self
    {
        return $this->attribute(AttributeName::NAME, $name);
    }

    public function size(int $size): self
    {
        return $this->attribute(AttributeName::SIZE, $size);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute(AttributeName::ALIASES, $aliases);
    }
}
