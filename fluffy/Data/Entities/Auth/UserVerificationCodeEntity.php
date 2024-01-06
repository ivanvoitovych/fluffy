<?php

namespace Fluffy\Data\Entities\Auth;

use Fluffy\Data\Entities\BaseEntity;

class UserVerificationCodeEntity extends BaseEntity
{
    public int $UserId;
    public string $Code;
    public string $CodeHash;
    public ?int $Expire = null;
}
