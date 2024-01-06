<?php

namespace Fluffy\Data\Entities\Migrations;

use Fluffy\Data\Entities\BaseEntityMap;
use Fluffy\Data\Entities\CommonMap;

class MigrationHistoryEntityMap extends BaseEntityMap
{
    public static string $Table = 'Migration';

    public static array $Indexes = [
        'UX_Key' => [
            'Columns' => ['Key'],
            'Unique' => true
        ]
    ];

    public static function Columns(): array
    {
        return [
            'Id' => CommonMap::$Id,
            'Key' => CommonMap::$VarChar255,
            'CreatedOn' => CommonMap::$MicroDateTime,
            'CreatedBy' => CommonMap::$VarChar255Null,
            'UpdatedOn' => CommonMap::$MicroDateTime,
            'UpdatedBy' => CommonMap::$VarChar255Null,
        ];
    }
}
