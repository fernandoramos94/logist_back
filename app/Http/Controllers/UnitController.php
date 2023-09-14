<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Unit::all();

        return response()->json($data, 200);
    }


    public function getData()
    {
        $data = Unit::select("id", "unit",)->where("active", 1)->get();
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
            'unit' => 'required',
            "plates" => 'required'
        ], [
            'name.unit' => 'El campo unidad es requerido',
            'plates.required' => 'El campo placas es requerido'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Unit::insert([
                "unit" => $request["unit"],
                "plates" => $request["plates"]
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
        $data = Unit::find($id);

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
        //
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
            'unit' => 'required',
            "plates" => 'required'
        ], [
            'name.unit' => 'El campo unidad es requerido',
            'plates.required' => 'El campo placas es requerido'
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Unit::where("id", $id)->update([
                "unit" => $request["unit"],
                "plates" => $request["plates"]
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
        Unit::where("id", $id)->delete();

        return response()->json(["msg" => "Se ha eliminado la unidad de forma existosa"], 200);
    }
}
