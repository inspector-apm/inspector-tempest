<?php

declare(strict_types=1);

namespace Inspector\Tempest\Tests;

use Inspector\Tempest\Arr;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Inspector\Tempest\Arr
 */
final class ArrTest extends TestCase
{
    public function testGetReturnsEntireArrayWhenKeyIsNull(): void
    {
        $array = ['foo' => 'bar'];
        $this->assertSame(['foo' => 'bar'], Arr::get($array, null));
    }

    public function testGetReturnsValueForExistingKey(): void
    {
        $array = ['foo' => 'bar'];
        $this->assertSame('bar', Arr::get($array, 'foo'));
    }

    public function testGetReturnsDefaultForMissingKey(): void
    {
        $array = ['foo' => 'bar'];
        $this->assertSame('default', Arr::get($array, 'missing', 'default'));
    }

    public function testGetReturnsNullForMissingKeyWithoutDefault(): void
    {
        $array = ['foo' => 'bar'];
        $this->assertNull(Arr::get($array, 'missing'));
    }

    public function testGetWithDotNotationForNestedArrays(): void
    {
        $array = [
            'user' => [
                'profile' => [
                    'name' => 'John',
                    'email' => 'john@example.com',
                ],
            ],
        ];

        $this->assertSame('John', Arr::get($array, 'user.profile.name'));
        $this->assertSame('john@example.com', Arr::get($array, 'user.profile.email'));
    }

    public function testGetWithDotNotationReturnsDefaultForMissingNestedKey(): void
    {
        $array = [
            'user' => [
                'profile' => [
                    'name' => 'John',
                ],
            ],
        ];

        $this->assertSame('default', Arr::get($array, 'user.profile.missing', 'default'));
        $this->assertNull(Arr::get($array, 'user.profile.missing'));
    }

    public function testGetWithDotNotationReturnsDefaultForMissingIntermediateKey(): void
    {
        $array = ['user' => ['name' => 'John']];

        $this->assertSame('default', Arr::get($array, 'user.profile.email', 'default'));
        $this->assertNull(Arr::get($array, 'user.profile.email'));
    }

    public function testGetReturnsArrayWhenNestedValueIsArray(): void
    {
        $array = [
            'user' => [
                'roles' => ['admin', 'editor'],
            ],
        ];

        $this->assertSame(['admin', 'editor'], Arr::get($array, 'user.roles'));
    }

    public function testGetWithKeyArrayReturnsFirstMatch(): void
    {
        $array = ['foo' => 'bar', 'baz' => 'qux'];

        $this->assertSame('bar', Arr::get($array, ['foo', 'missing'], 'default'));
        $this->assertSame('qux', Arr::get($array, ['missing', 'baz'], 'default'));
    }

    public function testGetWithKeyArrayReturnsDefaultWhenNoMatch(): void
    {
        $array = ['foo' => 'bar'];

        $this->assertSame('default', Arr::get($array, ['missing1', 'missing2'], 'default'));
    }

    public function testGetHandlesIntegerKeys(): void
    {
        $array = ['foo', 'bar', 'baz'];

        $this->assertSame('foo', Arr::get($array, 0));
        $this->assertSame('bar', Arr::get($array, 1));
        $this->assertSame('baz', Arr::get($array, 2));
    }

    public function testGetHandlesEmptyStringAsKey(): void
    {
        $array = ['' => 'empty_key'];

        $this->assertSame('empty_key', Arr::get($array, ''));
    }

    public function testSetReplacesArrayWhenKeyIsNull(): void
    {
        $array = ['foo' => 'bar'];
        $result = Arr::set($array, null, ['new' => 'value']);

        $this->assertSame(['new' => 'value'], $array);
        $this->assertSame(['new' => 'value'], $result);
    }

    public function testSetSetsValueForSimpleKey(): void
    {
        $array = ['foo' => 'bar'];
        Arr::set($array, 'foo', 'new_value');

        $this->assertSame('new_value', $array['foo']);
    }

    public function testSetAddsNewSimpleKey(): void
    {
        $array = ['foo' => 'bar'];
        Arr::set($array, 'new_key', 'new_value');

        $this->assertSame('new_value', $array['new_key']);
    }

    public function testSetWithDotNotationForNestedArrays(): void
    {
        $array = ['user' => ['profile' => ['name' => 'John']]];
        Arr::set($array, 'user.profile.email', 'john@example.com');

        $this->assertSame('john@example.com', $array['user']['profile']['email']);
        $this->assertSame('John', $array['user']['profile']['name']);
    }

    public function testSetWithDotNotationCreatesIntermediateArrays(): void
    {
        $array = [];
        Arr::set($array, 'user.profile.email', 'john@example.com');

        $this->assertSame(['user' => ['profile' => ['email' => 'john@example.com']]], $array);
    }

    public function testSetWithDotNotationOverwritesNestedValue(): void
    {
        $array = ['user' => ['profile' => ['email' => 'old@example.com']]];
        Arr::set($array, 'user.profile.email', 'new@example.com');

        $this->assertSame('new@example.com', $array['user']['profile']['email']);
    }

    public function testSetWithArrayOfKeys(): void
    {
        $array = [];
        Arr::set($array, ['user', 'profile', 'name'], 'John');

        $this->assertSame('John', $array['user']['profile']['name']);
    }

    public function testSetPreservesOtherNestedValues(): void
    {
        $array = [
            'user' => [
                'profile' => [
                    'name' => 'John',
                    'age' => 30,
                ],
            ],
        ];

        Arr::set($array, 'user.profile.email', 'john@example.com');

        $this->assertSame('John', $array['user']['profile']['name']);
        $this->assertSame(30, $array['user']['profile']['age']);
        $this->assertSame('john@example.com', $array['user']['profile']['email']);
    }

    public function testSetHandlesIntegerKeys(): void
    {
        $array = [0 => 'foo', 1 => 'bar'];
        Arr::set($array, 1, 'baz');

        $this->assertSame('baz', $array[1]);
        $this->assertSame('foo', $array[0]);
    }

    public function testSetReturnsTheModifiedArray(): void
    {
        $array = ['foo' => 'bar'];
        $result = Arr::set($array, 'baz', 'qux');

        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $result);
        $this->assertSame(['foo' => 'bar', 'baz' => 'qux'], $array);
    }

    public function testSetDoesNotModifyNonArrayIntermediateValues(): void
    {
        $array = ['user' => 'string_value'];
        Arr::set($array, 'user.profile.name', 'John');

        $this->assertIsArray($array['user']);
        $this->assertSame('John', $array['user']['profile']['name']);
    }
}
