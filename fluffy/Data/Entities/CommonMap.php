<?php

namespace Fluffy\Data\Entities;

class CommonMap
{
    public static string $OnDeleteNoAction = 'NoAction';
    public static string $OnDeleteCascade = 'Cascade';
    public static string $OnDeleteRestrict = 'Restrict';
    public static string $OnDeleteSetNull = 'SetNull';
    public static string $OnDeleteSetDefault = 'SetDefault';

    public static array $Id =  [
        'type' => 'bigint',
        'null' => false,
        'autoIncrement' => true
    ];
    public static array $Int =  [
        'type' => 'integer',
        'null' => false
    ];
    public static array $IntNull =  [
        'type' => 'integer',
        'null' => true
    ];
    public static array $BigInt =  [
        'type' => 'bigint',
        'null' => false
    ];
    public static array $BigIntNull =  [
        'type' => 'bigint',
        'null' => true
    ];
    public static array $MicroDateTime = [
        'type' => 'bigint',
        'null' => false,
    ];
    public static array $VarChar255Null = [
        'type' => 'character varying',
        'length' => 255,
        'null' => true,
    ];
    public static array $VarChar255 = [
        'type' => 'character varying',
        'length' => 255,
        'null' => false,
    ];
    public static array $Boolean = [
        'type' => 'boolean',
        'default' => 'false',
        'null' => false,
    ];
}
