<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Auth\UserEntity;
use Fluffy\Data\Entities\Auth\UserEntityMap;
use DotDi\Attributes\Inject;
use Fluffy\Data\Repositories\BasePostgresqlRepository;

#[Inject(['entityType' => UserEntity::class, 'entityMap' => UserEntityMap::class])]
class UserRepository extends BasePostgresqlRepository
{
}
