<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request){
        $validation_rules = [
            'name' => 'nullable|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];

        $messages = [
            'name.unique' => 'The name already exists.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field is not valid.',
            'email.unique' => 'The email is already registered.', 

        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()], 422);
        }

        $name = $request->filled('name') ? $request->name : 'Anonymous';

        $user = User::create([
            'name' => $name,
            'email' => $request-> email,
            'password' => Hash::make($request->password),
        ])->assignRole('player');

        $acessToken = $user->createToken('authToken')->accessToken;
       
        return response()->json([ 
        'user' => $user,
        'acessToken' => $acessToken,
        ], 201); 

        //return response()->json(['message' => 'Register completed'], 201); 
    }
    
    public function login (Request $request){
         
    }
}
