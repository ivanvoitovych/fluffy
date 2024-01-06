<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Auth\UserVerificationCodeEntity;
use Fluffy\Data\Entities\Auth\UserVerificationCodeEntityMap;
use DotDi\Attributes\Inject;
use Fluffy\Data\Repositories\BasePostgresqlRepository;

#[Inject(['entityType' => UserVerificationCodeEntity::class, 'entityMap' => UserVerificationCodeEntityMap::class])]
class UserVerificationCodeRepository extends BasePostgresqlRepository
{
}
