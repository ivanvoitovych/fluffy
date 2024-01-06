<?php

namespace Fluffy\Data\Context;

use Fluffy\Data\Entities\BaseEntityMap;

class DbQuery
{
    public string $entityType;
    /**
     * 
     * @var string | BaseEntityMap
     */
    public string $entityMap;

    public function __construct(private DbContext $db)
    {
    }

    public function from(string $entityType, string $entityMap)
    {
        $this->entityType = $entityType;
        $this->entityMap = $entityMap;
        return $this;
    }

    public function getAll()
    {
        return $this->db->execute($this);
    }
}
