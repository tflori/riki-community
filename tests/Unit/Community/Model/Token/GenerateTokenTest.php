<?php

namespace Test\Unit\Community\Model\Token;

use Community\Model\Token\AbstractToken;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\Token\PasswordResetToken;
use Community\Model\Token\RememberToken;
use Test\TestCase;

class GenerateTokenTest extends TestCase
{
    /** @dataProvider provideTokenLengths
     * @param int $length
     * @test */
    public function returnsStringsInGivenLength(int $length)
    {
        $token = AbstractToken::generateToken($length);

        self::assertCount($length, str_split($token));
    }

    public function provideTokenLengths()
    {
        return [
            [1],
            [4],
            [8],
            [20],
        ];
    }

    /** @dataProvider provideAlphabets
     * @param array $alphabet
     * @test */
    public function generatesTokensFromAlphabet(array $alphabet)
    {
        $token = AbstractToken::generateToken(count($alphabet), implode('', $alphabet));

        foreach (str_split($token) as $char) {
            self::assertContains($char, $alphabet);
        }
    }

    public function provideAlphabets()
    {
        return [
            [['a', 'b']],
            [['a', 'A', 'b', 'B', 'c', 'C']],
            [['0', '1', '2', '3', '4', '5', '6']],
            [['!','"','$','%','&','/','(',')','=','?']],
        ];
    }

    /** @dataProvider provideRestrictedCharsForCode
     * @param string $char
     * @test */
    public function activationCodesNeverHaveChar(string $char)
    {
        for ($i = 0; $i < 10; $i++) {
            $token = ActivationCode::generateToken();

            self::assertNotContains($char, $token);
        }
    }

    public function provideRestrictedCharsForCode()
    {
        return [
            ['i'],
            ['I'],
            ['l'],
            ['0'],
            ['O'],
        ];
    }

    /** @dataProvider provideTokenModelsWithLength
     * @param string $class
     * @param int $length
     * @test */
    public function generatesTokensWithLength(string $class, int $length)
    {
        $token = $class::generateToken();

        self::assertCount($length, str_split($token));
    }

    public function provideTokenModelsWithLength()
    {
        return [
            [ActivationCode::class, 6],
            [ActivationToken::class, 20],
            [PasswordResetToken::class, 20],
            [RememberToken::class, 20],
        ];
    }

    /** @test */
    public function prependsFirstCharWhenShort()
    {
        // execute multiple times because of randomness
        for ($i = 0; $i < 20; $i++) {
            $token = AbstractToken::generateToken(8, '01');

            self::assertCount(8, str_split($token));
        }
    }
}
