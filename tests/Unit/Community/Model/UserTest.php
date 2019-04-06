<?php

namespace Test\Unit\Community\Model;

use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\Token\PasswordResetToken;
use Community\Model\Token\RememberToken;
use Community\Model\User;
use Test\TestCase;

class UserTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->ormAllowFetch(ActivationCode::class)[0]->byDefault();
        $this->ormAllowFetch(ActivationToken::class)[0]->byDefault();
        $this->ormAllowFetch(PasswordResetToken::class)[0]->byDefault();
        $this->ormAllowFetch(RememberToken::class)[0]->byDefault();
    }

    /** @dataProvider provideNotAllowedStatuses
     * @param string $current
     * @param string $new
     * @test */
    public function throwsWhenStatusNotAllowed(string $current, string $new)
    {
        $user = new User(['account_status' => $current]);

        self::expectException(\LogicException::class);
        self::expectExceptionMessage(sprintf('Transition to "%s" is not possible from "%s"', $new, $current));

        $user->accountStatus = $new;
    }

    public function provideNotAllowedStatuses()
    {
        return [
            [User::ACTIVATED, User::PENDING],
            [User::DISABLED, User::PENDING],
            [User::ARCHIVED, User::ACTIVATED],
            [User::ARCHIVED, User::DISABLED],
        ];
    }

    /** @test */
    public function initialStatusIsPending()
    {
        $user = new User();

        self::assertSame(User::PENDING, $user->accountStatus);
    }

    /** @dataProvider provideAllowedTransactions
     * @param $transaction
     * @param $current
     * @param $expected
     * @test */
    public function transactionsChangeStatus($transaction, $current, $expected)
    {
        $user = new User(['id' => 23, 'account_status' => $current]);

        $user->$transaction();

        self::assertSame($expected, $user->accountStatus);
    }

    public function provideAllowedTransactions()
    {
        return [
            ['activate', User::PENDING, User::ACTIVATED],
            ['disable', User::PENDING, User::DISABLED],
            ['delete', User::PENDING, User::ARCHIVED],
            ['disable', User::ACTIVATED, User::DISABLED],
            ['delete', User::ACTIVATED, User::ARCHIVED],
            ['activate', User::DISABLED, User::ACTIVATED],
            ['delete', User::DISABLED, User::ARCHIVED],
            ['restore', User::ARCHIVED, User::PENDING],
        ];
    }

    /** @test */
    public function activateChangesStatus()
    {
        $user = new User(['id' => 23]);

        $user->activate();

        self::assertSame(User::ACTIVATED, $user->accountStatus);
    }



    /** @test */
    public function throwsWhenTransitionIsInvalid()
    {
        $user = new User(['id' => 23, 'account_status' => USER::ARCHIVED]);

        self::expectException(\LogicException::class);
        self::expectExceptionMessage(sprintf('Unknown transition "%s" from "%s"', 'activate', User::ARCHIVED));

        $user->activate();
    }

    /** @test */
    public function removesActivationCodesOnActivate()
    {
        $user = new User(['id' => 23, 'account_status' => User::PENDING]);
        $activationCode = new ActivationToken();
        $this->ormExpectFetch(ActivationCode::class, [$activationCode]);

        $this->ormExpectDelete($activationCode);

        $user->activate();
    }

    /** @test */
    public function removesActivationTokensOnActivate()
    {
        $user = new User(['id' => 23, 'account_status' => User::PENDING]);
        $activationToken = new ActivationToken();
        $this->ormExpectFetch(ActivationToken::class, [$activationToken]);

        $this->ormExpectDelete($activationToken);

        $user->activate();
    }

    /** @test */
    public function disableRemovesAllRememberTokens()
    {
        $user = new User(['id' => 23, 'account_status' => User::ACTIVATED]);
        $rememberToken = new RememberToken();
        $this->ormExpectFetch(RememberToken::class, [$rememberToken]);

        $this->ormExpectDelete($rememberToken);

        $user->disable();
    }

    /** @test */
    public function disableRemovesAllPasswordResetTokens()
    {
        $user = new User(['id' => 23, 'account_status' => User::ACTIVATED]);
        $passwordResetToken = new PasswordResetToken();
        $this->ormExpectFetch(PasswordResetToken::class, [$passwordResetToken]);

        $this->ormExpectDelete($passwordResetToken);

        $user->disable();
    }
}
