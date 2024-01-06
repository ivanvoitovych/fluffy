<?php

namespace Application\Data\Repositories;

use Application\Data\Entities\SubFolder\EntityName;
use Application\Data\Entities\SubFolder\EntityNameMap;
use DotDi\Attributes\Inject;

#[Inject(['entityType' => EntityName::class, 'entityMap' => EntityNameMap::class])]
class EntityBaseNameRepository extends BasePostgresqlRepository
{
}
