<?php

namespace Test\PhpUnit;

use ArrayAccess;
use PHPUnit\Framework\Assert;
use PHPUnit\Util\InvalidArgumentHelper;
use Test\PhpUnit\Constraint\ArraySubset;

trait ArraySubsetAssert
{
    /**
     * Asserts that an array has a specified subset.
     *
     * @param array|ArrayAccess|mixed[] $subset
     * @param array|ArrayAccess|mixed[] $array
     * @param bool                      $strict Enable type checks (compare with ====)
     * @param string                    $message
     * @param float                     $delta Check that the difference of floats is greater $delta
     */
    public static function assertArraySubset(
        $subset,
        $array,
        bool $strict = false,
        string $message = '',
        float $delta = null
    ): void {
        if (! (is_array($subset) || $subset instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                1,
                'array or ArrayAccess'
            );
        }
        if (! (is_array($array) || $array instanceof ArrayAccess)) {
            throw InvalidArgumentHelper::factory(
                2,
                'array or ArrayAccess'
            );
        }
        $constraint = new ArraySubset($subset, $strict, $delta);
        Assert::assertThat($array, $constraint, $message);
    }
}
