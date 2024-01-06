<?php

namespace Fluffy\Data\Entities\Auth;

use Fluffy\Data\Entities\BaseEntity;
use Fluffy\Services\UtilsService;

class SessionEntity extends BaseEntity
{
    public string $HashId;
    public ?string $CSRF = null;
    public ?string $UserId = null;
    public bool $RememberMe = false;
    public ?string $CodeFor2FA = null;

    public static function getNew(): self
    {
        $session = new self();
        $session->HashId = UtilsService::randomHex(32);
        $session->CSRF = UtilsService::randomHex(32);
        return $session;
    }
}
