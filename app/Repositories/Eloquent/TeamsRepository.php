<?php

namespace App\Repositories\Eloquent;

use App\Models\Team;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Eloquent\BaseRepository;

class TeamsRepository extends BaseRepository implements ITeam
{
    // public function all()
    // {
    //     return Design::all();
    // }
    public function model(){
        return Team::class; //App\Models\Designs
    }

    public function fetchUserTeams(){
        return auth()->user()->teams;

    }

    
}