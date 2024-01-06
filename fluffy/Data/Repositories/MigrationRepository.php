<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Entities\Migrations\MigrationHistoryEntity;
use Fluffy\Data\Entities\Migrations\MigrationHistoryEntityMap;
use DotDi\Attributes\Inject;

#[Inject(['entityType' => MigrationHistoryEntity::class, 'entityMap' => MigrationHistoryEntityMap::class])]
class MigrationRepository extends BasePostgresqlRepository
{
}
