<?php

namespace App\Repositories\Eloquent;

use App\Models\Invitation;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Eloquent\BaseRepository;

class InvitationRepository extends BaseRepository implements IInvitation
{
    // public function all()
    // {
    //     return Design::all();
    // }
    public function model(){
        return Invitation::class; //App\Models\Designs
    }
    public function addUserToTeam($team, $user_id){
        $team->members()->attach($user_id);

    }

    public function removeUserFromTeam($team, $user_id){
        $team->members()->detach($user_id);
    }
 
    
}