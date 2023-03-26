<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;
    public function attemptLogin(Request $request){
        $token = $this->guard()->attempt($this->credentials($request));
        if(!$token){
            return false;
        }
        //get the authenticated user
        $user = $this->guard()->user();

        if($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()){
            return false;
        }

        //set the user's token
        $this->guard()->setToken($token);

        return true;
        
    }//end
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);
        //get the token from the authentication guard (JWT)
        $token = (string)$this->guard()->getToken();
        
        //extract the expiry date of the  token
        $expiration = $this->guard()->getPayload()->get('exp');

        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expiration
        ]);
    }//end
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()){
            return response()->json(["errors"=>[
                "verification" => "You need to verify your email acc."
            ]], 422);
        }
        throw ValidationException::withMessages([
            $this->username() => "Authentication failed"
        ]);
    }//end
    public function logout(){
        $this->guard()->logout();
        return response()->json(['message'=>'Logged out.']);
    }//end
    
    
}
