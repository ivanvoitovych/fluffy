<?php

namespace Fluffy\Data\Entities\Auth;

use Fluffy\Data\Entities\BaseEntityMap;
use Fluffy\Data\Entities\CommonMap;

class UserEntityMap extends BaseEntityMap
{
    public const PROPERTY_UserName = 'UserName';
    public const PROPERTY_Active = 'Active';
    public const PROPERTY_EmailConfirmed = 'EmailConfirmed';
    public const PROPERTY_Password = 'Password';

    public static string $Table = 'User';
    public static array $Indexes = [
        'UX_UserName' => [
            'Columns' => ['UserName'],
            'Unique' => true
        ]
    ];
    public static function Columns(): array
    {
        return  [
            'Id' => CommonMap::$Id,
            'UserName' => CommonMap::$VarChar255,
            'FirstName' => CommonMap::$VarChar255Null,
            'LastName' => CommonMap::$VarChar255Null,
            'Email' => CommonMap::$VarChar255Null,
            'Password' => CommonMap::$VarChar255Null,
            'Active' => CommonMap::$Boolean,
            'EmailConfirmed' => CommonMap::$Boolean,
            'CreatedOn' => CommonMap::$MicroDateTime,
            'CreatedBy' => CommonMap::$VarChar255Null,
            'UpdatedOn' => CommonMap::$MicroDateTime,
            'UpdatedBy' => CommonMap::$VarChar255Null,
        ];
    }
}
