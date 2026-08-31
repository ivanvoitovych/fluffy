<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Migrations\MigrationHistoryEntity;
use Fluffy\Data\Entities\Migrations\MigrationHistoryEntityMap;
use DotDi\Attributes\Inject;

/** @extends BasePostgresqlRepository<MigrationHistoryEntity> */
#[Inject(['entityType' => MigrationHistoryEntity::class, 'entityMap' => MigrationHistoryEntityMap::class])]
class MigrationRepository extends BasePostgresqlRepository
{
}
