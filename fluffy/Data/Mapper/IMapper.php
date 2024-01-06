<?php

namespace Fluffy\Data\Mapper;

interface IMapper
{
    function map(string $type, object $stdObject, $instance = null);
    function mapAssoc(string $type, array $arr, $instance = null);
}