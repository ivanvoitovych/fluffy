<?php

namespace Fluffy\Data\Repositories;

use Fluffy\Data\Connector\IConnector;
use Fluffy\Data\Entities\BaseEntity;
use Fluffy\Data\Entities\BaseEntityMap;
use Fluffy\Data\Entities\CommonMap;
use Fluffy\Data\Mapper\IMapper;
use RuntimeException;

class BasePostgresqlRepository
{
    /**
     * 
     * @param IMapper $mapper 
     * @param IConnector $connector 
     * @param BaseEntity|string $entityType 
     * @param BaseEntityMap|string $entityMap 
     * @return void 
     */
    public function __construct(private IMapper $mapper, private IConnector $connector, private string $entityType, private string $entityMap)
    {
    }

    static function GetTime(): int
    {
        $timeOfDay = gettimeofday();
        return $timeOfDay['sec'] * 1000000 + $timeOfDay['usec'];
    }

    public function getList(
        int $page = 1,
        ?int $size = 10,
        ?string $ordering = BaseEntityMap::PROPERTY_CreatedOn,
        int $order = 1, // -1 DESC
    ) {
        $pg = $this->connector->get();

        $select = '';
        $comma = '';
        foreach ($this->entityMap::Columns() as $property => $_) {
            $select .= "$comma\"{$property}\"";
            $comma = ', ';
        }
        $orderBy = $ordering !== null ? ("ORDER BY \"$ordering\"" . ($order > 0 ? " ASC" : " DESC")) : '';
        $limit = '';
        if ($size !== null) {
            $offset = ($page - 1) * $size;
            $limit = "LIMIT $size OFFSET $offset";
        }
        $sql = "SELECT $select FROM {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" $orderBy $limit";
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAll(SW_PGSQL_ASSOC);
        $list = $arr ? array_map(fn ($row) => $row ? $this->mapper->mapAssoc($this->entityType, $row) : null, $arr) : [];
        $countSql = "SELECT COUNT(*) as \"count\" FROM {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\"";
        $stmt = $pg->query($countSql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        $count = $arr['count'];
        return ['list' => $list, 'total' => $count];
    }

    public function getById($Id): ?BaseEntity
    {
        $pg = $this->connector->get();

        $select = '';
        $comma = '';
        foreach ($this->entityMap::Columns() as $property => $_) {
            $select .= "$comma\"{$property}\"";
            $comma = ', ';
        }
        $keyName = $this->entityMap::$PrimaryKeys[0];
        $primaryKeyCondition = "\"{$keyName}\" = $Id";

        $sql = "SELECT $select FROM {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" WHERE $primaryKeyCondition";
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        $entity = $arr ? $this->mapper->mapAssoc($this->entityType, $arr) : null;
        return $entity;
    }

    public function find(string $findKey, $value)
    {
        $select = '';
        $comma = '';
        foreach ($this->entityMap::Columns() as $property => $_) {
            $select .= "$comma\"{$property}\"";
            $comma = ', ';
        }
        $pg = $this->connector->get();
        $where = "\"{$findKey}\" = {$pg->escapeLiteral($value)}";
        $sql = "SELECT $select FROM {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" WHERE $where";
        // echo $sql . PHP_EOL;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        $entity = $arr ? $this->mapper->mapAssoc($this->entityType, $arr) : null;
        return $entity;
    }

    public function create(BaseEntity $entity)
    {
        $columns = '';
        $values = '';
        $comma = '';
        $now = self::GetTime();
        $entity->CreatedOn = $now;
        $entity->UpdatedOn = $now;
        $keyName = $this->entityMap::$PrimaryKeys[0];
        $pg = $this->connector->get();
        foreach ($this->entityMap::Columns() as $property => $_) {
            if ($property !== $keyName) {
                $columns .= "$comma\"{$property}\"";
                $value = $entity->{$property};
                if (is_bool($entity->{$property})) {
                    $value = $entity->{$property} ? 'true' : 'false';
                } else if ($entity->{$property} === null) {
                    $value = 'NULL';
                } else if (is_integer($entity->{$property})) {
                    $value = $entity->{$property};
                } else if (is_float($entity->{$property})) {
                    $value = number_format($entity->{$property}, 8, '.', '');
                } else {
                    $value = $pg->escapeLiteral($entity->{$property});
                }
                $values .= "$comma{$value}";
                $comma = ', ';
            }
        }
        $sql = "INSERT INTO {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" (" . PHP_EOL . '    ' . $columns . PHP_EOL . ')';
        $sql .= '    VALUES' . PHP_EOL . "($values) RETURNING \"$keyName\";";
        // echo $sql . PHP_EOL;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        if ($arr) {
            $entity->Id = $arr[$keyName];
            return true;
        }
        return false;
    }

    public function update(BaseEntity $entity, ?array $columnsToUpdate = null)
    {
        $columns = '';
        $comma = '';
        $now = self::GetTime();
        $entity->UpdatedOn = $now;
        $keyName = $this->entityMap::$PrimaryKeys[0];
        $pg = $this->connector->get();
        $hasCustom = $columnsToUpdate !== null;
        if ($hasCustom) {
            $columnsToUpdate[] = 'UpdatedOn';
            $columnsToUpdate[] = 'UpdatedBy';
        }
        foreach ($columnsToUpdate ?? $this->entityMap::Columns() as $property => $_) {
            if ($hasCustom) {
                $property = $_;
            }
            if ($property !== $keyName) {
                $value = $entity->{$property};
                if (is_bool($entity->{$property})) {
                    $value = $entity->{$property} ? 'true' : 'false';
                } else if ($entity->{$property} === null) {
                    $value = 'NULL';
                } else if (is_integer($entity->{$property})) {
                    $value = $entity->{$property};
                } else if (is_float($entity->{$property})) {
                    $value = number_format($entity->{$property}, 8, '.', '');
                } else {
                    $value = $pg->escapeLiteral($entity->{$property});
                }
                $columns .= "$comma\"{$property}\" = $value";
                $comma = ', ';
            }
        }
        $where = "WHERE \"{$this->entityMap::$Table}\".\"$keyName\" = {$entity->Id}";
        $sql = "UPDATE {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" SET " . PHP_EOL . '    ' . $columns . PHP_EOL . " $where;";
        // echo $sql . PHP_EOL;
        // return true;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->affectedRows();
        if ($arr) {
            return true;
        }
        return false;
    }

    public function delete(BaseEntity $entity)
    {
        $keyName = $this->entityMap::$PrimaryKeys[0];
        $pg = $this->connector->get();
        $where = "WHERE \"{$this->entityMap::$Table}\".\"$keyName\" = {$entity->Id}";
        $sql = "DELETE FROM {$this->entityMap::$Schema}.\"{$this->entityMap::$Table}\" $where;";
        // echo $sql . PHP_EOL;
        // return true;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->affectedRows();
        if ($arr) {
            return true;
        }
        return false;
    }

    public function metaData()
    {
        $pg = $this->connector->get();
        return $pg->metaData($this->entityMap::$Table);
    }

    public function dropTable(bool $cascade, bool $ifExists): bool
    {
        $tableName = $this->entityMap::$Table;
        $schema = $this->entityMap::$Schema;
        $pg = $this->connector->get();
        $cascadeSql = $cascade ? ' CASCADE' : '';
        $ifExistsSql = $ifExists ? ' IF EXISTS' : '';
        $sql = "DROP TABLE$ifExistsSql $schema.\"$tableName\"$cascadeSql";
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        // $arr = $stmt->fetchAssoc();
        return true;
    }

    public function addColumns(array $columnsSchema)
    {
        $tableName = $this->entityMap::$Table;
        $schema = $this->entityMap::$Schema;
        $columns = '';
        $comma = '';
        $pg = $this->connector->get();
        foreach ($columnsSchema as $property => $columnMeta) {
            $dataType = $columnMeta['type'];
            if (isset($columnMeta['length'])) {
                $dataType .= "({$columnMeta['length']})";
            }
            if (isset($columnMeta['null']) && $columnMeta['null'] === false) {
                $dataType .= " NOT NULL";
            }
            if (isset($columnMeta['default'])) {
                $dataType .= " DEFAULT " . $columnMeta['default'];
            }
            if (isset($columnMeta['autoIncrement'])) {
                $dataType .= " GENERATED ALWAYS AS IDENTITY";
            }
            $columns .= "{$comma}ADD COLUMN \"{$property}\" $dataType";
            $comma = ',' . PHP_EOL;
        }
        $sql = <<<EOD
        ALTER TABLE $schema."$tableName"
        $columns;
        EOD;

        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        // $arr = $stmt->fetchAssoc();
        return true;
    }

    /**
     * 
     * @return bool true if table created, false if already exists
     */
    public function createTable(
        ?array $columnsSchema = null,
        ?array $primaryKeys = null,
        ?array $indexesSchema = null,
        ?array $foreignKeysSchema = null
    ): bool {
        $tableName = $this->entityMap::$Table;
        $schema = $this->entityMap::$Schema;
        $columns = '';
        $comma = '';
        $dbUserName = $this->connector->getPool()->getUserName();
        $pg = $this->connector->get();
        foreach ($columnsSchema ?? $this->entityMap::Columns() as $property => $columnMeta) {
            $dataType = $columnMeta['type'];
            if (isset($columnMeta['length'])) {
                $dataType .= "({$columnMeta['length']})";
            }
            if (isset($columnMeta['null']) && $columnMeta['null'] === false) {
                $dataType .= " NOT NULL";
            }
            if (isset($columnMeta['default'])) {
                $dataType .= " DEFAULT " . $columnMeta['default'];
            }
            if (isset($columnMeta['autoIncrement'])) {
                $dataType .= " GENERATED ALWAYS AS IDENTITY";
            }
            $columns .= "$comma\"{$property}\" $dataType, " . PHP_EOL;
            $comma = '    ';
        }
        $pk = "";
        $comma = '';
        foreach ($primaryKeys ?? $this->entityMap::$PrimaryKeys as $columnName) {
            $pk .= "$comma\"{$columnName}\"";
            $comma = ', ';
        }
        if ($pk) {
            $pk = "    CONSTRAINT \"{$tableName}_PK\" PRIMARY KEY ($pk)";
        }
        $comma = ',' . PHP_EOL . '    ';
        $constrains = '';
        // foreach ($this->entityMap::$Indexes as $name => $indexMeta) {
        //     if ($indexMeta['Unique']) {
        //         $constrains .= "{$comma}CONSTRAINT \"$name\"";

        //         $constrains .= " UNIQUE";

        //         $constrains .= ' (';
        //         foreach ($indexMeta['Columns'] as $column) {
        //             $constrains .= "\"$column\"";
        //         }
        //         $constrains .= ')';
        //     }
        // }

        $comma = PHP_EOL . PHP_EOL;
        $indexes = '';
        foreach ($indexesSchema ?? $this->entityMap::$Indexes as $name => $indexMeta) {
            $indexName = "{$tableName}_$name";
            $unique = '';
            if ($indexMeta['Unique']) {
                $unique = " UNIQUE";
            }
            $indexColumns = '';
            $columnComma = '';
            foreach ($indexMeta['Columns'] as $column) {
                $indexColumns .= "$columnComma\"$column\" ASC NULLS LAST";
                $columnComma = ', ';
            }
            $indexSql = <<<EOD
            CREATE{$unique} INDEX IF NOT EXISTS "$indexName"
                ON $schema."$tableName" USING btree
                ($indexColumns);
            EOD;
            $indexes .= $comma . $indexSql;
        }
        $foreignKeys = '';
        $comma = ',' . PHP_EOL . '    ';
        foreach ($foreignKeysSchema ?? [] as $FKDefinition) {
            $fkSql = 'FOREIGN KEY (';
            $columnComma = '';
            foreach ($FKDefinition['Columns'] as $column) {
                $fkSql .= "\"$column\"";
                $columnComma = ', ';
            }
            $fkSql .= ') REFERENCES ';
            /** @var BaseEntityMap $otherTable */
            $otherTable = $FKDefinition['Table'];
            $otherSchema = $otherTable::$Schema;
            $otherTableName = $otherTable::$Table;
            $fkSql .= "$otherSchema.\"$otherTableName\" (";
            // c1, c2)
            $columnComma = '';
            foreach ($FKDefinition['References'] as $column) {
                $fkSql .= "\"$column\"";
                $columnComma = ', ';
            }
            $fkSql .= ')';
            switch ($FKDefinition['OnDelete']) {
                case CommonMap::$OnDeleteCascade: {
                        $fkSql .= ' ON DELETE CASCADE';
                        break;
                    }
                case CommonMap::$OnDeleteRestrict: {
                        $fkSql .= ' ON DELETE RESTRICT';
                        break;
                    }
                case CommonMap::$OnDeleteSetNull: {
                        $fkSql .= ' ON DELETE SET NULL';
                        break;
                    }
                case CommonMap::$OnDeleteSetDefault: {
                        $fkSql .= ' ON DELETE SET DEFAULT ';
                        break;
                    }
                case CommonMap::$OnDeleteNoAction:
                default: {
                        // nothing
                        break;
                    }
            }
            $foreignKeys .= $comma . $fkSql;
        }
        $sql = <<<EOD
        CREATE TABLE IF NOT EXISTS $schema."$tableName"
        (
            $columns{$pk}{$constrains}{$foreignKeys}
        );
        
        ALTER TABLE IF EXISTS $schema."$tableName"
            OWNER to $dbUserName;        
        $indexes
        EOD;
        // echo $sql . PHP_EOL;
        // return true;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        // $arr = $stmt->fetchAssoc();
        return true;
    }

    public function tableExist()
    {
        $tableName = $this->entityMap::$Table;
        $schema = $this->entityMap::$Schema;
        $pg = $this->connector->get();
        $sql = <<<EOD
        SELECT EXISTS (
                SELECT FROM 
                pg_tables
            WHERE 
                schemaname = '$schema' AND 
                tablename  = '$tableName'
        );
        EOD;
        $stmt = $pg->query($sql);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        return $arr['exists'];
    }

    public function executeSQL(string $sqlScript)
    {
        $pg = $this->connector->get();
        $stmt = $pg->query($sqlScript);
        if (!$stmt) {
            throw new RuntimeException("{$pg->error} {$pg->errCode}");
        }
        $arr = $stmt->fetchAssoc();
        return $arr;
    }
}
