<?php

namespace Components\Models\SubFolder;

class EntityNameModel extends BaseEntityNameModel
{
    public int $Id;

    public int $CreatedOn;
    public ?string $CreatedBy = null;

    public int $UpdatedOn;
    public ?string $UpdatedBy = null;
}
