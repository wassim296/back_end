<?php

namespace App\Http\Controllers;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);

        if(Auth::attempt($credentials)){
            return response()->json([
                'user' => Auth::user(),
                'message' => 'Success'
            ],200);

            return response()->json(['messsge' => 'Error'],401);
        }
    }
}
