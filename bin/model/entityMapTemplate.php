<?php

namespace Application\Data\Entities\SubFolder;

use Fluffy\Data\Entities\BaseEntityMap;
use Fluffy\Data\Entities\CommonMap;

class EntityNameMap extends BaseEntityMap
{
    public static string $Table = 'EntityTable';
    public static array $Indexes = [];
    public static function Columns(): array
    {
        return  [
            'Id' => CommonMap::$Id,
            'Column1' => CommonMap::$VarChar255,

            'CreatedOn' => CommonMap::$MicroDateTime,
            'CreatedBy' => CommonMap::$VarChar255Null,
            'UpdatedOn' => CommonMap::$MicroDateTime,
            'UpdatedBy' => CommonMap::$VarChar255Null,
        ];
    }
}
