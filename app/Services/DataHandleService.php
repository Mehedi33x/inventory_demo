<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class DataHandleService
{
    public function store(Model $model, array $data): Model
    {
        return $model->create($data);
    }

    public function update(Model $model, array $data): bool
    {
        return $model->update($data);
    }
}
