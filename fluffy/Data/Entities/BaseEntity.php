<?php

namespace Fluffy\Data\Entities;

abstract class BaseEntity
{
    public int $Id;

    public int $CreatedOn;
    public ?string $CreatedBy = null;

    public int $UpdatedOn;
    public ?string $UpdatedBy = null;
}
