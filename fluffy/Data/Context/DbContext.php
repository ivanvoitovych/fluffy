<?php

namespace Fluffy\Data\Context;

use Fluffy\Data\Connector\IConnector;
use Fluffy\Data\Mapper\IMapper;
use RuntimeException;

class DbContext
{
    public function __construct(private IMapper $mapper, private IConnector $connector)
    {
    }

    public function from(string $entityType, ?string $entityMap = null)
    {
        return (new DbQuery($this))->from($entityType, $entityMap ?? EntitiesMap::$map[$entityType] ?? throw new RuntimeException("Entity map is not set for '$entityType'"));
    }

    public function execute(DbQuery $query)
    {
        $pg = $this->connector->get();
        $sql = $this->buildSQL($query);
        $stmt = $pg->query($sql);

        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }

        $arr = $stmt->fetchAll();

        $entities = $arr
            ? array_map(fn ($assoc) => $assoc ? $this->mapper->mapAssoc($query->entityType, $assoc) : null, $arr)
            : null;

        return $entities;
    }

    public function buildSQL(DbQuery $query): string
    {
        $select = '';
        $comma = '';
        // var_dump($emptyEntity);
        // $emptyEntity->me;
        // var_dump($emptyEntity);
        foreach ($query->entityMap::Columns() as $property => $_) {
            $select .= "$comma\"{$property}\"";
            $comma = ', ';
        }
        $keyName = $query->entityMap::$PrimaryKeys[0];
        // $primaryKeyCondition = "\"{$keyName}\" = $Id";
        $where = '';
        $orderBy = "ORDER BY \"{$keyName}\" ASC";
        $sql = "SELECT $select FROM {$query->entityMap::$Schema}.\"{$query->entityMap::$Table}\" $where $orderBy";
        return $sql;
    }
}
