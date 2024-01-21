<?php

namespace Fluffy\Migrations;

use Fluffy\Data\Repositories\MigrationRepository;

class InstallMigration extends BaseMigration
{
    function __construct(MigrationRepository $MigrationHistoryRepository)
    {
        parent::__construct($MigrationHistoryRepository);
    }

    public function up()
    {
        // nothing
    }

    public function down()
    {
        // nothing
    }

    public function runUp()
    {
        if (!$this->MigrationHistoryRepository->tableExist()) {
            // Needs super user
            // $this->MigrationHistoryRepository->executeSQL('CREATE EXTENSION citext');
            $created = $this->MigrationHistoryRepository->createTable();
            if ($created) {
                echo "[Install] Installation has been completed. " . PHP_EOL;
            }
            $this->completeMigration($this->migrationName());
        }
    }
}
