<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Models\User;
use App\Repositories\Contracts\IUser;
use App\Repositories\Eloquent\BaseRepository;

class UserRepository extends BaseRepository implements IUser
{
    public function model(){
        return User::class; //App\Models\Designs
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }
}