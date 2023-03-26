<?php

namespace App\Repositories\Contracts;

interface IUser{
    public function all();

    public function findByEmail($email);
}