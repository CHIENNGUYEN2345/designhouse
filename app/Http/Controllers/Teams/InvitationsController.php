<?php

namespace App\Http\Controllers\Teams;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Repositories\Contracts\ITeam;
use App\Repositories\Contracts\IUser;
use App\Mail\SendInvitationToJoinTeam;
use App\Models\Team;
use App\Repositories\Contracts\IInvitation;
use PDO;

class InvitationsController extends Controller
{
    protected $invitations;
    protected $teams;
    protected $users;

    public function __construct(IInvitation $invitations, ITeam $teams, IUser $users)
    {
        $this->invitations = $invitations;
        $this->teams = $teams;
        $this->users = $users;
    }

    public function invite(Request $request, $teamId){
        //get the team
        $team = $this->teams->find($teamId);
        
        $this->validate($request, [
            'email' => ['required', 'email']
        ]);

        $user = auth()->user();

        //check if the user owns the team
        if(!$user->isOwnerOfTeam($team)){
            return response()->json([
                'email' => 'You are not the admin of this team.',
            ], 401);
        }

        //check if email has a pending invitation
        if($team->hasPendingInvite($request->email)){
            return response()->json([
                'email' => 'Email has already has a pending invite.',
            ], 422);
        }
        
        //get the recipient by email
        $recipient = $this->users->findByEmail($request->email);

        //if the recipient does not exist, send invitation to join the team
        if(!$recipient){
            $this->createInvitation(false, $team, $request->email);
            

            return response()->json([
                'message' => 'Invitation sent to user!'
            ], 200);
        }

        //if the user does exist
        //check if the team already has the user
        if($team->hasUser($recipient)){
            return response()->json([
                'message' => 'This user seems to be a team member already!'
            ], 422);
        }

        //if the user does exist, but not in the team
        //->send invitation to user
        $this->createInvitation(true, $team, $request->email);

        return response()->json([
            'message' => 'Invitation sent to user!'
        ], 200);

    }

    protected function createInvitation(bool $user_exists, Team $team, string $email){
        
        $invitation = $this->invitations->create([
            'team_id' => $team->id,
            'sender_id' => auth()->id(),
            'recipient_email' => $email,
            'token' => md5(uniqid(microtime()))
        ]);

        Mail::to($email)
                ->send(new SendInvitationToJoinTeam($invitation, $user_exists));

        return response()->json([
            'message' => 'Invitation sent to user!'
        ], 200);
    }

    public function resend($id){
        $invitation = $this->invitations->find($id);

        $this->authorize('resend',$invitation);

        // if(!auth()->user()->isOwnerOfTeam($invitation->team)){
        //     return response()->json([
        //         'email' => 'You are not the admin of this team.',
        //     ], 401);
        // };
        $recipient = $this->users->findByEmail($invitation->recipient_email);

        Mail::to($invitation->recipient_email)
                ->send(new SendInvitationToJoinTeam($invitation, !is_null($recipient)));

        return response()->json(['message'=>'Invitation resent...']);

    }

    public function respond(Request $request, $id){
        $this->validate($request, [
            'token' => ['required'],
            'decision' => ['required']
        ]);

        $token = $request->token;

        $decision = $request->decision;//'acept' or 'deny'

        $invitation = $this->invitations->find($id);

        //check if invitation belongs to this user
        // if($invitation->recipient_email !== auth()->user()->email){
        //     return response()->json([
        //         'message' => 'This is not your invitation'
        //     ], 401);
        // }

        $this->authorize('respond', $invitation);

        //check to make sure that the tokens match
        if($invitation->token !== $token){
            return response()->json([
                'message' => 'Sorry, Invalid token.'
            ], 401);
        }

        //check if accept
        if($decision !== 'deny'){
            $this->invitations->addUserToTeam($invitation->team, auth()->id());
        }

        $invitation->delete();

        return response()->json(['message'=>'Succesful'], 200);
    }

    public function destroy($id)
    {

        $invitation = $this->invitations->find($id);
        $this->authorize('delete',$invitation);
        $invitation->delete();
        return response()->json(['message'=>'Deleted.'], 200);
    }
}
