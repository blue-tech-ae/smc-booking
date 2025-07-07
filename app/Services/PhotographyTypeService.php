<?php

namespace App\Services;

use App\Models\PhotographyType;

class PhotographyTypeService
{
    public function store(array $data): PhotographyType
    {
        return PhotographyType::create($data);
    }
}
