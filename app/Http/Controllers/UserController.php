<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; 
use Validator;
use Illuminate\Support\Facades\Lang;
use App\Models\User;

class UserController extends Controller
{

    public function list(){
        $data = User::all();

        return response()->json($data, 200);
    }
    public function editAccount(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'user' => 'required', 
            'email' => 'required|email',
            'type_admin' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 

        $user = User::where("id", $id)->update([
            "name" => $input["name"],
            "user" => $input["user"],
            "email" => $input["email"],
            "type_admin" => $input["type_admin"],
            "status" => $input["status"]
        ]); 
        return response()->json(["msg" => "Se ha realizado la actualizacion de la cuenta de forma exitosa"], 200); 
    }

    public function updatePassword(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'password' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::where("id", $id)->update([
            "password" => $input["password"]
        ]); 
        return response()->json(["msg" => "Contraseña actualizada de forma exitosa"], 200); 
    }

    public function updateStatus(Request $request, $id){
        $validator = Validator::make($request->all(), [ 
            'status' => 'required',
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all();
        $user = User::where("id", $id)->update([
            "status" => $input["status"]
        ]); 
        return response()->json(["msg" => "Estado actualizado de forma exitosa"], 200); 
    }
    public function login(Request $request)
    {
        if($token = Auth::attempt(['user' => $request['user'], 'password' => $request['password']])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('tmhLogistc'); 
            
            if((int)$user->status == 1){
                return response()->json(['success' => $success, 'data' => $user], 200);  
            }else{
                return response()->json(['error'=>'El usuario se encuentra inactivo'], 401);
                 
            }
        } 
        else{ 
            return response()->json(['error'=>'Usuario o contraseña incorrecta'], 401); 
        } 
    }
    public function register(Request $request) 
    { 
        $validator = Validator::make($request->all(), [ 
            'name' => 'required', 
            'user' => 'required', 
            'email' => 'required|email', 
            'password' => 'required',
            'type_admin' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all(); 
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input); 
        return response()->json(["msg" => "Se ha creado el usuario de forma exitosa"], 200); 
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'token' => $user->createToken('tmhLogistc')->accessToken
        ]);
    }

    
}
