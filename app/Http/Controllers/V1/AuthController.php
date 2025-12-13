<?php

namespace App\Http\Controllers\V1;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;
class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users',
            'password' => 'required',
        ]);
        $fields['password'] =  Hash::make($fields['password']);

        $user = User::create($fields);

        $token = $user->createToken($request->name);
        return [
            'user' => $user, 
            'token' => $token->plainTextToken
        ];
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|string',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return ['message' => 'The provided credentials are incorrect.'];
        }
    
        $token = $user->createToken($user->name);
        
        //session(['user_id' => $user->id]); 

        $tokenFromRequest = PersonalAccessToken::findToken($token->plainTextToken);
        //$tokenFromRequest->user;
        return [
            'user' => $user, 
            'token' => $token->plainTextToken,
            'tokenFromRequest' => $tokenFromRequest,
            
        ];
    }

    public function logout(Request $request){
        //return "Ok";
        $request->user()->tokens()->delete();
        //session()->flush(); // removes all session data
        return ['message' => 'you are logged out',
            'loggedOut' => true,
            
        ];
        // $tokenString = $request->bearerToken(); // Just the token string, no "Bearer"
        // $tokenFromRequest = PersonalAccessToken::findToken($tokenString);
    }
}
