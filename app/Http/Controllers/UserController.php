<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Module;

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

    public function permissions(){
        // Deprecated placeholder; keeping for backward compatibility if used elsewhere.
        return response()->json([], 200);
    }

    /**
     * Get modules and actions permitted for a given user ID.
     * Rules:
     *  - A module is visible if the user's role has an active permission for it (permissions.is_active = true) and the module is active.
     *  - An action is visible if:
     *      a) There is an explicit action_permissions record for the user's role with is_active = true, OR
     *      b) There is NO action_permissions record for the user's role, and the parent module permission is active (inherit allow).
     *      If there is an explicit record with is_active = false, it must be excluded.
     */
    public function permissionsByUser($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        if (!$user->role) {
            return response()->json([
                'user' => $user,
                'role' => null,
                'modules' => [],
            ], 200);
        }

        $roleId = (int) $user->role_id;

        // Modules allowed for the role
        $modules = Module::query()
            ->where('is_active', true)
            ->whereHas('roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId)
                  ->where('permissions.is_active', true);
            })
            ->with(['actions' => function ($q) use ($roleId) {
                $q->where('actions.is_active', true)
                  ->where(function ($q2) use ($roleId) {
                      // Explicit allow
                      $q2->whereHas('roles', function ($q3) use ($roleId) {
                          $q3->where('roles.id', $roleId)
                             ->where('action_permissions.is_active', true);
                      })
                      // Or inherit allow when no explicit record exists for this role
                      ->orWhereDoesntHave('roles', function ($q4) use ($roleId) {
                          $q4->where('roles.id', $roleId);
                      });
                  })
                  ->orderBy('order');
            }])
            ->orderBy('order')
            ->get([
                'id', 'name', 'order', 'icon', 'route', 'parent_id', 'is_active'
            ]);

        // Build flat array of modules with actions
        $flat = [];
        foreach ($modules as $m) {
            $flat[$m->id] = [
                'id' => $m->id,
                'name' => $m->name,
                'icon' => $m->icon,
                'route' => $m->route,
                'order' => $m->order,
                'parent_id' => $m->parent_id,
                'actions' => $m->actions->map(function ($a) {
                    return [
                        'id' => $a->id,
                        'name' => $a->name,
                        'icon' => $a->icon,
                        'order' => $a->order,
                    ];
                })->values()->all(),
                'children' => [],
            ];
        }

        // Build tree using parent_id
        $tree = [];
        foreach ($flat as $id => &$node) {
            $parentId = $node['parent_id'];
            if ($parentId && isset($flat[$parentId])) {
                $flat[$parentId]['children'][] = &$node;
            } else {
                $tree[] = &$node;
            }
        }
        unset($node); // break reference

        // Sort children by order
        $sortByOrder = function (&$nodes) use (&$sortByOrder) {
            usort($nodes, function ($a, $b) { return ($a['order'] <=> $b['order']); });
            foreach ($nodes as &$n) {
                if (!empty($n['children'])) {
                    $sortByOrder($n['children']);
                }
            }
            unset($n);
        };
        $sortByOrder($tree);

        $payload = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role_id' => $user->role_id,
            ],
            'role' => [
                'id' => $user->role->id,
                'name' => $user->role->name,
            ],
            'modules' => array_values($tree),
        ];

        return response()->json($payload, 200);
    }
}
