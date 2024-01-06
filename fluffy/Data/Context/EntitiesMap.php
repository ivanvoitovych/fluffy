<?php

namespace Fluffy\Data\Context;

use Fluffy\Data\Entities\Auth\UserEntity;
use Fluffy\Data\Entities\Auth\UserEntityMap;

class EntitiesMap
{
    public static array $map = [
        UserEntity::class => UserEntityMap::class
    ];
}
