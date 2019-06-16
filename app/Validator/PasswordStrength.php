<?php

namespace App\Validator;

use Verja\Error;
use Verja\Validator;

class PasswordStrength extends Validator
{
    /** @var int */
    protected $minimalScore;

    /**
     * PasswordStrength constructor.
     *
     * @param int $minimalScore
     */
    public function __construct(int $minimalScore = 50)
    {
        $this->minimalScore = $minimalScore;
    }

    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        $score = $this->calculate($value);
        if ($score >= $this->minimalScore) {
            return true;
        }

        $this->error = new Error(
            'PASSWORD_TO_WEAK',
            $value,
            sprintf('password strength %d; %d needed', $score, $this->minimalScore),
            [
                'minimalScore' => $this->minimalScore,
                'score' => $score
            ]
        );

        return false;
    }

    /**
     * Calculate score for a password
     *
     * @param  string $password
     * @return int
     */
    public function calculate($password)
    {
        $length    = strlen($password);
        $score     = $length * 4;
        $nUpper    = 0;
        $nLower    = 0;
        $nNum      = 0;
        $nSymbol   = 0;
        $locUpper  = array();
        $locLower  = array();
        $locNum    = array();
        $locSymbol = array();
        $charDict  = array();

        // count character classes
        for ($i = 0; $i < $length; ++$i) {
            $ch   = $password[$i];
            $code = ord($ch);
            if ($code >= 48 && $code <= 57) { // 0-9
                $nNum++;
                $locNum[] = $i;
            } elseif ($code >= 65 && $code <= 90) { // A-Z
                $nUpper++;
                $locUpper[] = $i;
            } elseif ($code >= 97 && $code <= 122) { // a-z
                $nLower++;
                $locLower[] = $i;
            } else {
                $nSymbol++;
                $locSymbol[] = $i;
            }

            if (!isset($charDict[$ch])) {
                $charDict[$ch] = 1;
            } else {
                $charDict[$ch]++;
            }
        }

        // reward upper/lower characters if pw is not made up of only either one
        if ($nUpper !== $length && $nLower !== $length) {
            if ($nUpper !== 0) {
                $score += ($length - $nUpper) * 2;
            }
            if ($nLower !== 0) {
                $score += ($length - $nLower) * 2;
            }
        }

        // reward numbers if pw is not made up of only numbers
        if ($nNum !== $length) {
            $score += $nNum * 4;
        }

        // reward symbols
        $score += $nSymbol * 6;

        // middle number or symbol
        foreach (array($locNum, $locSymbol) as $list) {
            $reward = 0;
            foreach ($list as $i) {
                $reward += ($i !== 0 && $i !== $length -1) ? 1 : 0;
            }
            $score += $reward * 2;
        }

        // chars only
        if ($nUpper + $nLower === $length) {
            $score -= $length;
        }

        // numbers only
        if ($nNum === $length) {
            $score -= $length;
        }

        // repeating chars
        $repeats = 0;
        foreach ($charDict as $count) {
            if ($count > 1) {
                $repeats += $count - 1;
            }
        }

        if ($repeats > 0) {
            $score -= (int) (floor($repeats / ($length-$repeats)) + 1);
        }

        if ($length > 2) {
            // consecutive letters and numbers
            foreach (array('/[a-z]{2,}/', '/[A-Z]{2,}/', '/[0-9]{2,}/') as $re) {
                preg_match_all($re, $password, $matches, PREG_SET_ORDER);
                if (!empty($matches)) {
                    foreach ($matches as $match) {
                        $score -= (strlen($match[0]) - 1) * 2;
                    }
                }
            }

            // sequential letters
            $locLetters = array_merge($locUpper, $locLower);
            sort($locLetters);
            foreach ($this->findSequence($locLetters, mb_strtolower($password)) as $seq) {
                if (count($seq) > 2) {
                    $score -= (count($seq) - 2) * 2;
                }
            }

            // sequential numbers
            foreach ($this->findSequence($locNum, mb_strtolower($password)) as $seq) {
                if (count($seq) > 2) {
                    $score -= (count($seq) - 2) * 2;
                }
            }
        }

        return $score;
    }

    /**
     * Find all sequential chars in string $src
     *
     * Only chars in $charLocations are considered. $charLocations is a list of numbers.
     * For example if $charLocations is [0,2,3], then only $src[2:3] is a possible
     * substring with sequential chars.
     *
     * @param  array  $charLocations
     * @param  string $src
     * @return array  [[c,c,c,c], [a,a,a], ...]
     */
    private function findSequence($charLocations, $src)
    {
        $sequences = array();
        $sequence  = array();
        for ($i = 0; $i < count($charLocations) - 1; ++$i) {
            $here         = $charLocations[$i];
            $next         = $charLocations[$i + 1];
            $charHere     = $src[$charLocations[$i]];
            $charNext     = $src[$charLocations[$i + 1]];
            $distance     = $next - $here;
            $charDistance = ord($charNext) - ord($charHere);
            if ($distance === 1 && $charDistance === 1) {
                // We find a pair of sequential chars!
                if (empty($sequence)) {
                    $sequence = array($charHere, $charNext);
                } else {
                    $sequence[] = $charNext;
                }
            } elseif (!empty($sequence)) {
                $sequences[] = $sequence;
                $sequence    = array();
            }
        }
        if (!empty($sequence)) {
            $sequences[] = $sequence;
        }
        return $sequences;
    }
}
