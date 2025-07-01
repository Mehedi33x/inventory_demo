<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class UserService
{
    public function store(Model $model,array $data){
        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $model->create($data);
    }
}