<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

class EnumType extends ComplexType
{
    public function __construct()
    {
        parent::__construct(TypeName::ENUM);
    }

    public function name(string $name): self
    {
        return $this->attribute(AttributeName::NAME, $name);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute(AttributeName::NAMESPACE, $namespace);
    }

    public function aliases(string $alias, string ...$aliases): self
    {
        \array_unshift($aliases, $alias);

        return $this->attribute(AttributeName::ALIASES, $aliases);
    }

    public function doc(string $doc): self
    {
        return $this->attribute(AttributeName::DOC, $doc);
    }

    public function symbols(string $symbol, string ...$symbols): self
    {
        \array_unshift($symbols, $symbol);

        return $this->attribute(AttributeName::SYMBOLS, $symbols);
    }

    public function default(string $default): self
    {
        return $this->attribute(AttributeName::DEFAULT, $default);
    }
}
