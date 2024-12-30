<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;

use Laravel\Sanctum\PersonalAccessToken;



class AuthController extends Controller
{

    /**
     * @param  \App\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    
    /**
     * Registration of the specified user.
     */
    public function register(UserRequest $request) 
    {

        //Data validation
        $validatedData = $request->validated();

        //Save user
        $user = User::create($validatedData);

        return response()->json(
            [
                'user' => $user,
                'message' => 'User Registered Successfully'
            ], 201);
    }

    
    /**
     * Login of the specified user.
     */
    public function login(Request $request) 
    {
        //Data validation
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);


        //Get user by email
        $user = User::where('email', $validatedData['email'])->first();

        //Verifies the user's existence
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        //Check if the passwords received are not equal to the one hashed in the database.
        else if (!Hash::check($validatedData['password'], $user->password )) {     
            return response()->json(['message' => 'The provided credentials are incorrect'], 401);
        }

        //If the credentials are correct, the token is generated
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 200);


    }


    /**
     * Display the specified user.
     */
    public function profile(Request $request)
    {
        //Obtains the token from the header (Authorization: Bearer {token})
        $token = $request->bearerToken(); 
        
        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 400);
        }

        //Finds the token in the database
        $personalAccessToken = PersonalAccessToken::findToken($token);

        //Verifies the existence of the token
        if (!$personalAccessToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        //Obtains the user associated with the token
        $user = $personalAccessToken->tokenable;

        return response()->json(
        [
            'user' => $user,
            'message' => 'User Successfully Found',
        ], 200);

    }

    
    /**
     * Logout of the specified user.
     */
    public function logout(Request $request) 
    {
        // Revoke all tokens
		$request->user()->tokens()->delete();

		// Revoke the current token
		$request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
        
    }
}