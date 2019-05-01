<?php

namespace Test\Unit\Validator;

use App\Validator\PasswordStrength;
use Test\TestCase;

class PasswordStrengthTest extends TestCase
{
    /** @dataProvider provideAcceptablePasswords
     * @param int $min
     * @param string $password
     * @test */
    public function returnsTrueForAcceptablePassword($min, $password)
    {
        $validator = new PasswordStrength($min);

        $result = $validator->validate($password);

        self::assertTrue($result);
    }

    public function provideAcceptablePasswords()
    {
        return [
            [ 100, 'str0ngP4ssw0rd!' ],
            [ 90, 'str0ngP4ssw0rd' ],
            [ 50, 'str0ngPw' ],
            [ 20, '4sdF' ],
            [ 10, '5312' ],
        ];
    }

    /** @dataProvider provideUnacceptablePasswords
     * @param int $min
     * @param string $password
     * @test */
    public function returnsFalseForUnacceptablePassword($min, $password)
    {
        $validator = new PasswordStrength($min);

        $result = $validator->validate($password);

        self::assertFalse($result);
    }

    public function provideUnacceptablePasswords()
    {
        return [
            [ 100, 'str0ngP4ssw0rd' ],
            [ 60, 'str0ngPw' ],
            [ 30, '4sdF' ],
            [ 20, '5312' ],
            [ 20, 'aifj' ],
            [ 20, 'abcdef' ],
            [ 20, '123456' ],
        ];
    }
}
