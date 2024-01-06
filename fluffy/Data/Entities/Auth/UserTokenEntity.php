<?php

namespace Fluffy\Data\Entities\Auth;

use Fluffy\Data\Entities\BaseEntity;

class UserTokenEntity extends BaseEntity
{
    public int $UserId;
    public string $Token;
    public string $TokenHash;
    public ?int $Expire = null;
    public ?int $LastVisit = null;
}
