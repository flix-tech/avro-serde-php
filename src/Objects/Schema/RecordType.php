<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Objects\Schema;

use FlixTech\AvroSerializer\Objects\Schema;
use FlixTech\AvroSerializer\Objects\Schema\Record\Field;
use FlixTech\AvroSerializer\Objects\Schema\Record\FieldOption;

class RecordType extends ComplexType
{
    /**
     * @var array<Field>
     */
    private $fields = [];

    public function __construct()
    {
        parent::__construct('record');
    }

    public function name(string $name): self
    {
        return $this->attribute('name', $name);
    }

    public function namespace(string $namespace): self
    {
        return $this->attribute('namespace', $namespace);
    }

    public function doc(string $doc): self
    {
        return $this->attribute('doc', $doc);
    }

    /**
     * @param array<string> $aliases
     */
    public function aliases(array $aliases): self
    {
        return $this->attribute('aliases', $aliases);
    }

    public function field(string $name, Schema $type, FieldOption ...$options): self
    {
        $record = clone $this;
        $record->fields[] = new Field($name, $type, ...$options);

        return $record;
    }

    /**
     * @return array<mixed>
     */
    public function serialize(): array
    {
        $record = parent::serialize();

        $record['fields'] = \array_map(function (Field $field) {
            return $field->serialize();
        }, $this->fields);

        return $record;
    }
}
