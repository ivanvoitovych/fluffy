<?php

namespace Fluffy\Data\Entities;

abstract class BaseEntityMap
{
    public static string $Table = '';
    public static string $Schema = 'public';
    public static array $PrimaryKeys = ['Id'];
    public static array $Indexes = [];
    public static array $Ignore = [];
    public static function ForeignKeys(): array
    {
        return [];
    }
    public static function Columns(): array
    {
        return [
            'Id' => CommonMap::$Id,
            'CreatedOn' => CommonMap::$MicroDateTime,
            'CreatedBy' => CommonMap::$VarChar255Null,
            'UpdatedOn' => CommonMap::$MicroDateTime,
            'UpdatedBy' => CommonMap::$VarChar255Null,
        ];
    }
}
