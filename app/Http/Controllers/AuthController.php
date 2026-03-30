<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Exception;

class AuthController extends Controller
{
    public function login(Request $request){
        $credentials = $request->validate([
            'email'=>'required|email',
            'password'=>'required',
        ]);
      

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                    'user' => $user,
                    'token' => $token,
                    'message' => 'Success'
            ],200);
            }
            return response()->json(['messsge' => 'Error'],401);
    }

   public function register(Request $request)
    {
        
        $request->validate([
        'name'     => 'required|string|max:150',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $email = $request->email; 
    $userExists = User::where('email' , $email)->first() ;

    if ($userExists) {
        return response()->json([
            'status' => 'error',
            'message' => 'Cet email existe déjà'
        ], 422);
    }

    try {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)

        ]);



       return response()->json([
            'status' => 'success',
            'message' => 'Compte créé avec succès ! Connectez-vous.'
        ], 201);
}catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Une erreur technique est survenue.'
        ], 500);
    }

    
    }

public function getUserById($id)
{
    $user = User::findOrFail($id);

    return response()->json([
        'user' => $user 
    ]);
}
public function logout(Request $request)
{
    $user = Auth::guard('sanctum')->user();

    if ($user) {
       
        $user->tokens()->delete(); 

        return response()->json(['message' => 'Success: Tokens deleted']);
    }

    return response()->json(['message' => 'Error: User not found by Sanctum'], 401);
}

public function searchUsers(Request $request)
{
    $query = $request->input('search'); 

    $users = User::where('name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->get();

    return response()->json([
        'users' => $users
    ]);
}


    public function updateRole(Request $request ){

        if(Auth::user()->role !== 'admin'){
            return response()->json([
                'message' => 'action non autorisee',
            ],403);
            }

            $request->validate([
                'idUser.*' => 'required',
                'role' => 'required|in:admin,professor,student'
            ]);

            $user = User::findOrFail($request->idUser);
            $user->update([
                'role' => $request->role
            ]);

        return response()->json([
            'message' => 'L utilisateur sélectionné est update',
        
        ], 200);
        }
public function updateProfile(Request $request)
{
    $userId = auth()->id();

    $user = User::findOrfail($userId);

    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => [
            'required', 
            'string', 
            'email', 
            'max:255', 
            Rule::unique('users')->ignore($user->id) 
        ],
    ]);

    $data = $request->all();

    $user->update($data);

        // $user->name = $request->name;
        // $user->email = $request->email;
        // $user->save();

        return response()->json([
            'success' => true,
            'user'    => $user,
            'message' => 'Profil meryel!'
        ], 200);
    }
}

    

