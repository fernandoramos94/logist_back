<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Driver::where("active",1)->get();

        foreach ($data as $item) {
            $item->bank_data = json_decode($item->bank_data);
        }

        return response()->json($data, 200);
    }

    public function getData()
    {
        $data = Driver::select("id", "name", "last_name")->where("active",1)->get();
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            "last_name" => 'required',
            'cell_phone' => 'required',
            'birthday_date' => 'required|date',
            'municipality' => 'required',
            'bank_data' => 'required',
            'bank_data.holder_name' => 'required',
            'bank_data.bank_name' => 'required',
            'bank_data.account_number' => 'required',
            'bank_data.account_type' => 'required'
        ], [
            'name.required' => 'El campo nombre es requerido',
            'last_name.required' => 'El campo apellido es requerido',
            'cell_phone.required' => 'El campo celular es requerido',
            'local_regime.required' => 'El campo regimen fiscal es requerido',
            'birthday_date.required' => 'El campo fecha es nacimiento es requerido',
            'birthday_date.date' => 'Ingrese una fecha valida para el campo fecha nacimiento',
            'municipality.required' => 'El campo alcaldia o municipio es requerido',
            'bank_data.required' => 'Ingrese los datos bancarios del conductor',
            'bank_data.holder_name.required' => 'El campo nombre del titular es requerido',
            'bank_data.bank_name.required' => 'El campo banco es requerido',
            'bank_data.account_number.required' => 'El campo numero de cuento o tarjeta es requerido',
            'bank_data.account_type.required' => 'El campo tipo de cuenta es requerido'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Driver::insert([
                "name" => $request["name"],
                "last_name" => $request["last_name"],
                "cell_phone" => $request["cell_phone"],
                "birthday_date" => $request["birthday_date"],
                "municipality" => $request["municipality"],
                "bank_data" => json_encode($request["bank_data"])
            ]);

            return response()->json(["msg" => "Se ha insertado el registro de forma existosa."], 200);
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
        $data = Driver::find($id);

        if($data){
            $data->bank_data = json_decode($data->bank_data);
        }
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
            "last_name" => 'required',
            'cell_phone' => 'required',
            'birthday_date' => 'required',
            'municipality' => 'required',
            'bank_data' => 'required',
            'bank_data.holder_name' => 'required',
            'bank_data.bank_name' => 'required',
            'bank_data.account_number' => 'required',
            'bank_data.account_type' => 'required'
        ], [
            'name.required' => 'El campo nombre es requerido',
            'last_name.required' => 'El campo apellido es requerido',
            'cell_phone.required' => 'El campo celular es requerido',
            'local_regime.required' => 'El campo regimen fiscal es requerido',
            'birthday_date.required' => 'El campo fecha es nacimiento es requerido',
            'municipality.required' => 'El campo alcaldia o municipio es requerido',
            'bank_data.required' => 'Ingrese los datos bancarios del conductor',
            'bank_data.holder_name.required' => 'El campo nombre del titular es requerido',
            'bank_data.bank_name.required' => 'El campo banco es requerido',
            'bank_data.account_number.required' => 'El campo numero de cuento o tarjeta es requerido',
            'bank_data.account_type.required' => 'El campo tipo de cuenta es requerido'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Driver::where("id", $id)->update([
                "name" => $request["name"],
                "last_name" => $request["last_name"],
                "cell_phone" => $request["cell_phone"],
                "birthday_date" => $request["birthday_date"],
                "municipality" => $request["municipality"],
                "bank_data" => json_encode($request["bank_data"])
            ]);

            return response()->json(["msg" => "Se ha actualizado el registro de forma existosa."], 200);
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
        $delete = Driver::where("id", $id)>update(["active" => false]);

        return response()->json(["msg" => "Se ha eliminado el recurso de forma existosa"], 200);
    }
}
