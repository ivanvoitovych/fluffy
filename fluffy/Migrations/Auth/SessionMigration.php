<?php

namespace Fluffy\Migrations\Auth;

use Fluffy\Data\Entities\CommonMap;
use Fluffy\Data\Repositories\MigrationRepository;
use Fluffy\Data\Repositories\SessionRepository;
use Fluffy\Migrations\BaseMigration;

class SessionMigration extends BaseMigration
{
    function __construct(MigrationRepository $MigrationHistoryRepository, private SessionRepository $sessionRepository)
    {
        parent::__construct($MigrationHistoryRepository);
    }

    public function up()
    {
        $this->sessionRepository->createTable(
            [
                'Id' => CommonMap::$Id,

                'HashId' => CommonMap::$VarChar255,
                'CSRF' => CommonMap::$VarChar255Null,
                'UserId' => CommonMap::$BigIntNull,
                'RememberMe' => CommonMap::$Boolean,
                'CodeFor2FA' => CommonMap::$VarChar255Null,

                'CreatedOn' => CommonMap::$MicroDateTime,
                'CreatedBy' => CommonMap::$VarChar255Null,
                'UpdatedOn' => CommonMap::$MicroDateTime,
                'UpdatedBy' => CommonMap::$VarChar255Null,
            ],
            ['Id'],
            [
                'UX_HashId' => [
                    'Columns' => ['HashId'],
                    'Unique' => true
                ],
                'IX_UserId' => [
                    'Columns' => ['UserId'],
                    'Unique' => false
                ]
            ]
        );
    }

    public function down()
    {
        $this->sessionRepository->dropTable(true, true);
    }
}
