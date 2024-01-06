<?php

namespace Fluffy\Models\Users;

class UserModel
{
    public int $Id;
    public string $UserName;
    public ?string $FirstName = null;
    public ?string $LastName = null;
    public ?string $Email = null;
    public bool $Active = false;
    public bool $EmailConfirmed = false;
}
