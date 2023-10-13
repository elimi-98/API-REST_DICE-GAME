<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

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
            'password.min' => 'The password must contain a minimum of 8 characters.',
            'password.required' => 'The password field is required.',

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

        $accessToken = $user->createToken('authToken')->accessToken;
       
       /**return response()->json([ 
        'user' => $user,
        'accessToken' => $accessToken,
        ], 201); **/

        return response()->json(['message' => 'Register completed'], 201); 
    }
    
    public function login (Request $request){
        
        $validation_rules = [
            'email' => 'required|email:users',
            'password' => 'required|min:8',
        ];

        $messages = [
            
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field is not valid.',
            'password.required' => 'The password field is required.',
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()], 422);
        }; 

        $login_data =[
            'email' => $request-> email,
            'password' => $request-> password,
        ];

        if(Auth::attempt($login_data)){
            
            $user = Auth::user();
             /** @var \App\Models\User $user **/ //decimos que $user es una instancia de clase User
            $accessToken = $user-> createToken('authToken')->accessToken;

                return response()->json([
                    
                    'message' => 'You have logged in successfully!',
                    'user' => $user,
                    'accessToken' => $accessToken,
                ], 200);
            } else {
                // Las credenciales de inicio de sesión son incorrectas
                return response()->json([
                    'message' => 'Incorrect credentials',
                ], 401);
            }

        }
    
}
