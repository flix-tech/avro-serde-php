<?php

declare(strict_types=1);

namespace FlixTech\AvroSerializer\Test\Objects\SchemaResolvers;

use FlixTech\AvroSerializer\Objects\SchemaResolvers\CallableResolver;
use FlixTech\AvroSerializer\Test\AbstractFunctionalTestCase;

use function Widmogrod\Functional\constt;

class CallableResolverTest extends AbstractFunctionalTestCase
{
    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     * @throws \AvroSchemaParseException
     */
    public function it_should_use_callable_for_resolving_value_schemas(): void
    {
        $resolver = new CallableResolver(constt($this->avroSchema));

        $this->assertEquals($resolver->valueSchemaFor('anyData'), $this->avroSchema);
        $this->assertNull($resolver->keySchemaFor('anyData'));
    }

    /**
     * @test
     *
     * @throws \AvroSchemaParseException
     */
    public function it_should_use_callable_for_resolving_key_schemas(): void
    {
        $resolver = new CallableResolver(constt($this->avroSchema), constt($this->readersSchema));

        $this->assertEquals($resolver->keySchemaFor('anyData'), $this->readersSchema);
    }
}
