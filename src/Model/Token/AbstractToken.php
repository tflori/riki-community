<?php

namespace Community\Model\Token;

use Community\Model\User;
use ORM\Entity;

abstract class AbstractToken extends Entity
{
    const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const LENGTH = 20;

    protected static $relations = [
        'user' => [User::class, ['user_id' => 'id']],
    ];

    public static function generateToken(int $length = null, string $alphabet = null): string
    {
        $alphabet = $alphabet ?? static::ALPHABET;
        $base = strlen($alphabet);
        $length = $length ?? static::LENGTH;
        $cardinality = pow($base, $length);
        $bytes = ceil(log($cardinality, 256));

        $binary = random_bytes($bytes);
        $decimal = '0';
        foreach (str_split($binary) as $byte) {
            $decimal = bcadd(bcmul($decimal, '256'), ord($byte));
        }

        $len = 0;
        $result = '';
        do {
            $len++;
        } while (bccomp(bcpow($base, $len), $decimal) !== 1);

        for ($pos = $len - 1; $pos > 0; $pos--) {
            $factor = bcpow($base, $pos);
            $result .= $alphabet{bcdiv($decimal, $factor, 0)};
            $decimal = bcmod($decimal, $factor);
        }
        $result .= $alphabet{(int)$decimal};

        if (strlen($result) > $length) {
            // return end when length > expected
            return substr($result, -$length);
        } elseif (strlen($result) < $length) {
            // prepend first char from alphabet when length < expected
            return str_repeat($alphabet{0}, $length - strlen($result)) . $result;
        } else {
            return $result;
        }
    }
}
