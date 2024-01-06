<?php

namespace Fluffy\Data\Entities\Auth;

use Fluffy\Data\Entities\BaseEntityMap;
use Fluffy\Data\Entities\CommonMap;

class SessionEntityMap extends BaseEntityMap
{
    public const PROPERTY_HashId = 'HashId';
    public const PROPERTY_UserId = 'UserId';

    public static string $Table = 'Session';
    public static array $Indexes = [
        'UX_HashId' => [
            'Columns' => ['HashId'],
            'Unique' => true
        ],
        'IX_UserId' => [
            'Columns' => ['UserId'],
            'Unique' => false
        ]
    ];
    public static function Columns(): array
    {
        return  [
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
        ];
    }
}
