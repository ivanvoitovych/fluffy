<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Auth\SessionEntity;
use Fluffy\Data\Entities\Auth\SessionEntityMap;
use DotDi\Attributes\Inject;
use Fluffy\Data\Repositories\BasePostgresqlRepository;

#[Inject(['entityType' => SessionEntity::class, 'entityMap' => SessionEntityMap::class])]
class SessionRepository extends BasePostgresqlRepository
{
}
