<?php

declare(strict_types=1);

namespace Inspector\Tempest;

use function array_key_exists;
use function is_array;
use function array_shift;
use function count;
use function explode;
use function str_contains;

class Arr
{
    /**
     * Get an item from an array using "dot" notation.
     *
     * @param array<mixed> $array
     * @param array<string|int>|string|int|null $key
     */
    public static function get(array $array, mixed $key, mixed $default = null): mixed
    {
        if ($key === null) {
            return $array;
        }

        if (is_array($key)) {
            // Handle array of keys (first match wins)
            foreach ($key as $k) {
                $value = self::get($array, $k, $default);
                if ($value !== $default) {
                    return $value;
                }
            }
            return $default;
        }

        // If the key exists in the array directly, return it
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        // Handle dot notation
        if (!str_contains((string)$key, '.')) {
            return $default;
        }

        $segments = explode('.', (string)$key);
        $current = $array;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param array<mixed> $array
     * @param array<string|int>|string|int|null $key
     */
    public static function set(array &$array, mixed $key, mixed $value): array
    {
        if ($key === null) {
            return $array = $value;
        }

        $keys = is_array($key) ? $key : explode('.', (string)$key);

        $current = &$array;

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (!isset($current[$key]) || !is_array($current[$key])) {
                $current[$key] = [];
            }

            $current = &$current[$key];
        }

        $current[array_shift($keys)] = $value;

        return $array;
    }
}
