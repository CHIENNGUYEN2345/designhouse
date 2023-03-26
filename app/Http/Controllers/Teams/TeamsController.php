<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Repositories\Contracts\IInvitation;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;

class TeamsController extends Controller
{
    protected $teams;
    protected $users;
    protected $invitations;
    public function __construct(ITeam $teams, IUser $users, IInvitation $invitations)
    {
        $this->teams = $teams;
        $this->users = $users;
        $this->invitations = $invitations;
    }
    public function index(Request $request){

    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => ['required','string','max:80','unique:teams,name'],
        ]);

        //create teams in DB
        $team = $this->teams->create([
            'owner_id' =>auth()->id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        //current user is inserts as team member using boot method in Team model
        return new TeamResource($team);
    }
    
    public function update(Request $request, $id){
        $team = $this->teams->find($id);
        $this->authorize('update',$team);

        $this->validate($request,[
            'name'=>['required','string','max:80',
            'unique:teams,name,'.$id]
        ]);

        $this->teams->update($id,[
            'name'=>$request->name,
            'slug'=>Str::slug($request->name),

        ]);

        return new TeamResource($team);
    }

    public function findById($id){
        $team = $this->teams->find($id);
        return new TeamResource($team);
    }

    public function fetchUserTeams(){
        $teams= $this->teams->fetchUserTeams();
        return TeamResource::collection($teams); 
    }

    public function removeFromTeam($teamId, $userId){
        //get the team
        $team = $this->teams->find($teamId);
        $user = $this->users->find($userId);

        //check that the user is not the owner
        if($user->isOwnerOfTeam($team)){
            return response()->json([
                'message' => 'You are the team owner'
            ],401);
        }

        //check that the person sending the request
        //is either the owner of the team or the person
        //who wants to leave the team
        if(!auth()->user()->isOwnerOfTeam($team) && auth()->id() !== $user->id){
            return response()->json([
                'message' => 'You can not do this'
            ],401);
        }

        $this->invitations->removeUserFromTeam($team, $userId);

        return response()->json(['message'=>'Success'],200);
    }

    
}
