<?php

namespace Community\Model\Token;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Community\Model\User;
use ORM\Entity;
use ORM\EntityManager;

/**
 * Class AbstractToken
 *
 * @package Community\Model\Token
 * @author Thomas Flori <thflori@gmail.com>
 * @property int $id
 * @property int $userId
 * @property string $token
 * @property Carbon $validUntil
 * @property User $user
 */
abstract class AbstractToken extends Entity
{
    const ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    const LENGTH = 20;

    protected static $relations = [
        'user' => [User::class, ['user_id' => 'id']],
    ];

    /**
     * Create a new token for $user with $interval validity
     *
     * @param User   $user
     * @param string $interval
     *
     * @return AbstractToken
     */
    public static function newToken(User $user, string $interval): AbstractToken
    {
        $em = EntityManager::getInstance(static::class);
        do {
            $token   = static::generateToken();
            $fetcher = $em->fetch(static::class);
            $fetcher->where('token', $token);
        } while ($fetcher->count() > 0);

        $entity = new static;
        $entity->userId = $user->id;
        $entity->token = $token;
        $entity->validUntil = Carbon::now()->add(CarbonInterval::fromString($interval));
        return $entity;
    }

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

//    public function setValidUntil(Carbon $dt)
//    {
//        $this->data['valid_until'] = $dt->setTimezone('UTC')->format('Y-m-d\TH:i:s.u\Z');
//        return $this;
//    }
}
