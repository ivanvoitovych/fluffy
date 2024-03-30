<?php

namespace Components\Models\SubFolder;

use Components\Models\Validation\ValidationRules;

class EntityNameValidation
{
    public function __construct(private BaseEntityNameModel $model)
    {
    }

    public function getValidationRules()
    {
        return ValidationRules::rules($this->model)
            ->toList();
    }
}
