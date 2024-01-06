<?php

namespace Fluffy\Migrations\Auth;

use Fluffy\Data\Entities\CommonMap;
use Fluffy\Data\Repositories\MigrationRepository;
use Fluffy\Data\Repositories\UserTokenRepository;
use Fluffy\Migrations\BaseMigration;

class UserTokenMigration extends BaseMigration
{
    function __construct(MigrationRepository $MigrationHistoryRepository, private UserTokenRepository $userTokenRepository)
    {
        parent::__construct($MigrationHistoryRepository);
    }

    public function up()
    {
        $this->userTokenRepository->createTable(
            [
                'Id' => CommonMap::$Id,

                'UserId' => CommonMap::$BigInt,
                'Token' => CommonMap::$VarChar255,
                'TokenHash' => CommonMap::$VarChar255,
                'Expire' => CommonMap::$IntNull,
                'LastVisit' => CommonMap::$BigIntNull,

                'CreatedOn' => CommonMap::$MicroDateTime,
                'CreatedBy' => CommonMap::$VarChar255Null,
                'UpdatedOn' => CommonMap::$MicroDateTime,
                'UpdatedBy' => CommonMap::$VarChar255Null,
            ],
            ['Id'],
            [
                'IX_UserId' => [
                    'Columns' => ['UserId'],
                    'Unique' => false,
                ],
                'UX_TokenHash' => [
                    'Columns' => ['TokenHash'],
                    'Unique' => true
                ]
            ]
        );
    }

    public function down()
    {
        $this->userTokenRepository->dropTable(true, true);
    }
}
