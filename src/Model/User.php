<?php

namespace Community\Model;

use Carbon\Carbon;
use Community\Model\Concerns\WithCreated;
use Community\Model\Concerns\WithUpdated;
use Community\Model\Token\ActivationCode;
use Community\Model\Token\ActivationToken;
use Community\Model\Token\PasswordResetToken;
use Community\Model\Token\RememberToken;
use JsonSerializable;
use ORM\Entity;

/**
 * Class User
 *
 * @package Community\Model
 * @author Thomas Flori <thflori@gmail.com>
 *
 * @property int id
 * @property string name
 * @property string displayName
 * @property string password
 * @property string email
 * @property Carbon created
 * @property Carbon updated
 * @property string accountStatus
 * @property-read ActivationCode[] activationCodes
 * @property-read ActivationToken[] activationTokens
 * @property-read RememberToken[] rememberTokens
 * @property-read PasswordResetToken[] passwordResetTokens
 */
class User extends Entity implements JsonSerializable
{
    use WithCreated;
    use WithUpdated;

    const PENDING = 'pending';
    const ACTIVATED = 'activated';
    const DISABLED = 'disabled';
    const ARCHIVED = 'archived';

    const TRANSITIONS = [
        self::PENDING => [
            'activate' => self::ACTIVATED,
            'disable' => self::DISABLED,
            'delete' => self::ARCHIVED,
        ],
        self::ACTIVATED => [
            'disable' => self::DISABLED,
            'delete' => self::ARCHIVED,
        ],
        self::DISABLED => [
            'activate' => self::ACTIVATED,
            'delete' => self::ARCHIVED,
        ],
        self::ARCHIVED => [
            'restore' => self::PENDING,
        ],
    ];

    protected static $relations = [
        'activationCodes' => [ActivationCode::class, 'user'],
        'activationTokens' => [ActivationToken::class, 'user'],
        'rememberTokens' => [RememberToken::class, 'user'],
        'passwordResetTokens' => [PasswordResetToken::class, 'user'],
    ];

    protected $data = [
        'account_status' => self::PENDING,
    ];

    public static $bcryptCost = 10;

    public function setAccountStatus(string $newStatus)
    {
        $currentStatus = $this->accountStatus;
        if (!isset(self::TRANSITIONS[$currentStatus]) || !in_array($newStatus, self::TRANSITIONS[$currentStatus])) {
            throw new \LogicException(sprintf(
                'Transition to "%s" is not possible from "%s"',
                $newStatus,
                $currentStatus
            ));
        }

        $this->data['account_status'] = $newStatus;
    }

    public function setPassword(string $password)
    {
        $this->data['password'] = password_hash($password, PASSWORD_BCRYPT, ['cost' => self::$bcryptCost]);
    }

    public function activate()
    {
        $this->transition('activate');
    }

    public function disable()
    {
        $this->transition('disable');
    }

    public function delete()
    {
        $this->transition('delete');
    }

    public function restore()
    {
        $this->transition('restore');
    }

    protected function transition($transition)
    {

        $currentStatus = $this->accountStatus;
        if (!isset(self::TRANSITIONS[$currentStatus][$transition])) {
            throw new \LogicException(sprintf(
                'Unknown transition "%s" from "%s"',
                $transition,
                $currentStatus
            ));
        }

        if ($currentStatus === self::PENDING) {
            foreach (array_merge($this->activationTokens, $this->activationCodes) as $token) {
                $this->entityManager->delete($token);
            }
        } elseif ($currentStatus === self::ACTIVATED) {
            foreach (array_merge($this->rememberTokens, $this->passwordResetTokens) as $token) {
                $this->entityManager->delete($token);
            }
        }

        $this->accountStatus = self::TRANSITIONS[$currentStatus][$transition];
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'displayName' => $this->displayName,
            'email' => $this->email,
            'accountStatus' => $this->accountStatus,
            'avatar' => 'https://www.gravatar.com/avatar/' . md5($this->email) . '?s=24',
            'created' => $this->created->format('Y-m-d\TH:i:s.u\Z'),
            'updated' => $this->updated->format('Y-m-d\TH:i:s.u\Z'),
        ];
    }
}
