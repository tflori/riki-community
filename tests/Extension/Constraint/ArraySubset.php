<?php

namespace Test\Extension\Constraint;

use ArrayAccess;
use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;

class ArraySubset extends Constraint
{
    /**
     * @var iterable|mixed[]
     */
    private $subset;

    /**
     * @var bool
     */
    private $strict;

    /** @var float */
    protected $delta = null;

    public function __construct(iterable $subset, bool $strict = false, float $delta = null)
    {
        parent::__construct();
        $this->delta = $delta;
        $this->strict = $strict;
        $this->subset = $subset;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @return string
     */
    public function toString(): string
    {
        return 'has the subset ' . $this->exporter->export($this->subset);
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @return string
     * @throws InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return 'an array has the expected subset. Differences:' . PHP_EOL .
            json_encode($this->arrayDiffRecursive($this->subset, $other), JSON_PRETTY_PRINT);
    }

    /**
     * Test that
     * @param mixed $other
     *
     * @return bool
     */
    protected function matches($other): bool
    {
        return count($this->arrayDiffRecursive($this->subset, $other)) === 0;
    }

    /**
     * @param array  $expected
     * @param array  $actual
     * @param array  $difference
     * @param string $parentPath
     * @return array
     */
    public function arrayDiffRecursive($expected, $actual, array &$difference = [], $parentPath = '')
    {
        $oldKey = 'expected';
        $newKey = 'actual';
        foreach ($expected as $k => $v) {
            $path = ($parentPath ? $parentPath . '.' : '') . $k;
            if (is_array($v)) {
                if (!array_key_exists($k, $actual) || !is_array($actual[$k])) {
                    $difference[$path][$oldKey] = $v;
                    $difference[$path][$newKey] = null;
                } else {
                    $this->arrayDiffRecursive($v, $actual[$k], $difference, $path);
                    if (!empty($recursion)) {
                        $difference[$oldKey][$k] = $recursion[$oldKey];
                        $difference[$newKey][$k] = $recursion[$newKey];
                    }
                }
            } else {
                if (!empty($v) && !array_key_exists($k, $actual)) {
                    $difference[$path][$oldKey] = $v;
                    $difference[$path][$newKey] = null;
                } else {
                    $a = $actual[$k] ?? null;
                    if ($this->delta && is_float($v)) {
                        $diff = abs($a - $v);
                        if ($diff > $this->delta) {
                            $difference[$path][$oldKey] = $v;
                            $difference[$path][$newKey] = $a;
                        }
                    } elseif ($this->strict && $a !== $v || $a != $v) {
                        $difference[$path][$oldKey] = $v;
                        $difference[$path][$newKey] = $a;
                    }
                }
            }
        }
        return $difference;
    }
}
