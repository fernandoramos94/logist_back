<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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
            "status" => $input["status"] == true || $input["status"] == 1 || $input["status"] == '1' ? 1 : 0
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
    public function delete($id){
        $user = User::where("id", $id)->delete(); 
        return response()->json(["msg" => "Se ha eliminado la cuenta de usuario de forma exitosa"], 200); 
    }
    public function login(Request $request)
    {
        if($token = Auth::attempt(['user' => $request['user'], 'password' => $request['password']])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('tmhLogistc'); 

            $user->img = Storage::url($user->img);
            
            if((int)$user->status == 1){
                return response()->json(['success' => $success, 'data' => $user], 200);  
            }else{
                return response()->json(['error'=>'El usuario se encuentra inactivo', "data" => $user], 401);
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
        $pass = bcrypt($input['password']); 
        $user = User::insert([
            "name" => $input["name"],
            "user" => $input["user"],
            "email" => $input["email"],
            "type_admin" => $input["type_admin"],
            "password" => $pass,
            "status" => $input["status"]
        ]); ; 
        return response()->json(["msg" => "Se ha creado el usuario de forma exitosa", "data" => $input], 200); 
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

    public function uploadimage(Request $request, $id)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
        $file = $request->file('file');
        
        $extension = $file->getClientOriginalExtension();

        $check = in_array($extension, $allowedfileExtension);

        if ($check) {
            $path = $file->store('public/images');
            $name = $file->getClientOriginalName();
            User::where("id", $id)->update(["img" => $path]);
        } else {
            return response()->json(['invalid_file_format'], 422);
        }

        return response()->json(['file_uploaded'], 200);
    }

    
}
