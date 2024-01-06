<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Auth\UserTokenEntity;
use Fluffy\Data\Entities\Auth\UserTokenEntityMap;
use DotDi\Attributes\Inject;
use Fluffy\Data\Repositories\BasePostgresqlRepository;

#[Inject(['entityType' => UserTokenEntity::class, 'entityMap' => UserTokenEntityMap::class])]
class UserTokenRepository extends BasePostgresqlRepository
{
}
