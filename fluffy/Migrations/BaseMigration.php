<?php

namespace Fluffy\Migrations;

use Fluffy\Data\Entities\Migrations\MigrationHistoryEntity;
use Fluffy\Data\Repositories\MigrationRepository;
use Exception;
use Throwable;

abstract class BaseMigration
{
    function __construct(protected MigrationRepository $MigrationHistoryRepository)
    {
    }

    public abstract function up();
    public abstract function down();

    public function migrationName()
    {
        return str_replace(__NAMESPACE__ . '\\', '', static::class);
    }

    public function runUp()
    {
        $name = $this->migrationName();
        if (!$this->isMigrated($name)) {
            echo "Running migration $name.." . PHP_EOL;
            try {
                $this->up();
                $this->completeMigration($name);
            } catch (Throwable $t) {
                try {
                    echo "Failed. Roll back migration $name.." . PHP_EOL;
                    $this->down();
                } catch (Throwable $_) {
                }
                throw $t;
            }
            echo "Migration $name completed" . PHP_EOL;
        }
    }

    public function isMigrated(string $key): bool
    {
        return $this->MigrationHistoryRepository->find('Key', $key) !== null;
    }

    public function completeMigration(string $key)
    {
        $migration = $this->MigrationHistoryRepository->find('Key', $key);
        if ($migration !== null) {
            throw new Exception("Migration $key has been ran already.");
        }
        $migration = new MigrationHistoryEntity();
        $migration->Key = $key;
        $this->MigrationHistoryRepository->Create($migration);
    }
}
