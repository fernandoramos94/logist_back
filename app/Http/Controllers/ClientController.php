<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::where("active", 1)->get();

        foreach ($clients as $item) {
            $item->destination_address = json_decode($item->destination_address);
            $item->origin_address = json_decode($item->origin_address);
        }

        return response()->json($clients, 200);
    }

    public function getData()
    {
        $clients = Client::select("id", "name", "destination_address", "origin_address")->where("active",1)->get();

        foreach ($clients as $item) {
            $item->destination_address = json_decode($item->destination_address);
            $item->origin_address = json_decode($item->origin_address);
        }

        return response()->json($clients, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            "rfc" => 'required',
            'postal_code' => 'required',
            'local_regime' => 'required',
            'email' => 'required|email',
            'destination_address' => 'required',
            'origin_address' => 'required'
        ], [
            'name.required' => 'El campo nombre cliente es requerido',
            'rfc.required' => 'El campo RFC es requerido',
            'postal_code.required' => 'El campo codigo postal es requerido',
            'local_regime.required' => 'El campo regimen fiscal es requerido',
            'email.required' => 'El campo correo electronico es requerido',
            'email.email' => 'Ingrese un correo electronico valido',
            'destination_address.required' => 'Ingrese al menos un direccion de destino',
            'origin_address.required' => 'Ingrese al menos un direccion de origen',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Client::insert([
                "name" => $request["name"],
                "rfc" => $request["rfc"],
                "postal_code" => $request["postal_code"],
                "local_regime" => $request["local_regime"],
                "email" => $request["email"],
                "destination_address" => json_encode($request["destination_address"]),
                "origin_address" => json_encode($request["origin_address"]),
            ]);
            return response()->json(["msg" => "El cliente se ha agregado de forma exitosa."], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Client::find($id);

        $data->destination_address = json_decode($data->destination_address);
        $data->origin_address = json_decode($data->origin_address);

        return response()->json($data, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            "rfc" => 'required',
            'postal_code' => 'required',
            'local_regime' => 'required',
            'email' => 'required|email',
            'destination_address' => 'required',
            'origin_address' => 'required'
        ], [
            'name.required' => 'El campo nombre cliente es requerido',
            'rfc.required' => 'El campo RFC es requerido',
            'postal_code.required' => 'El campo codigo postal es requerido',
            'local_regime.required' => 'El campo regimen fiscal es requerido',
            'email.required' => 'El campo correo electronico es requerido',
            'email.email' => 'Ingrese un correo electronico valido',
            'destination_address.required' => 'Ingrese al menos un direccion de destino',
            'origin_address.required' => 'Ingrese al menos un direccion de origen',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Client::where("id", $id)->update([
                "name" => $request["name"],
                "rfc" => $request["rfc"],
                "postal_code" => $request["postal_code"],
                "local_regime" => $request["local_regime"],
                "email" => $request["email"],
                "destination_address" => json_encode($request["destination_address"]),
                "origin_address" => json_encode($request["origin_address"]),
            ]);

            return response()->json(["msg" => "Datos actualizado de forma existosa."], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Client::where($id)>update(["active", false]);

        return response()->json(["msg" => "Se ha elimando el cliente de forma exitosa."], 200);

    }
}
