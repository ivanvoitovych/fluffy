<?php

namespace Application\Migrations\SubFolder;

use Fluffy\Data\Entities\CommonMap;
use Fluffy\Data\Repositories\MigrationRepository;
use Application\Data\Repositories\EntityBaseNameRepository;
use Fluffy\Migrations\BaseMigration;

class EntityBaseNameMigration extends BaseMigration
{
    function __construct(MigrationRepository $MigrationHistoryRepository, private EntityBaseNameRepository $entityBaseNameRepository)
    {
        parent::__construct($MigrationHistoryRepository);
    }

    public function up()
    {
        $this->entityBaseNameRepository->createTable(
            ['Id' => CommonMap::$Id,],
            ['Id'],
            [],
            []
        );
    }

    public function down()
    {
        $this->entityBaseNameRepository->dropTable(true, true);
    }
}
