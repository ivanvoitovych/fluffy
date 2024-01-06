<?php

namespace Fluffy\Migrations\Auth;

use Fluffy\Data\Entities\CommonMap;
use Fluffy\Data\Repositories\MigrationRepository;
use Fluffy\Data\Repositories\UserVerificationCodeRepository;
use Fluffy\Migrations\BaseMigration;

class UserVerificationCodeMigration extends BaseMigration
{
    function __construct(MigrationRepository $MigrationHistoryRepository, private UserVerificationCodeRepository $userVerificationCodeRepository)
    {
        parent::__construct($MigrationHistoryRepository);
    }

    public function up()
    {
        $this->userVerificationCodeRepository->createTable(
            [
                'Id' => CommonMap::$Id,

                'UserId' => CommonMap::$BigInt,
                'Code' => CommonMap::$VarChar255,
                'CodeHash' => CommonMap::$VarChar255,
                'Expire' => CommonMap::$IntNull,

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
                'UX_CodeHash' => [
                    'Columns' => ['CodeHash'],
                    'Unique' => true
                ]
            ]
        );
    }

    public function down()
    {
        $this->userVerificationCodeRepository->dropTable(true, true);
    }
}
