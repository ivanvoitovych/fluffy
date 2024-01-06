<?php

namespace Fluffy\Models\Auth;

use Fluffy\Data\Entities\Auth\UserEntity;

class RegisterResult
{
    public ?UserEntity $User = null;
    public bool $Success = false;
    public bool $UserNameTaken = false;
}
