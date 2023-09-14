<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::select("service.*", "client.name as client")->join("client", "service.client_id", "=", "client.id")->get();

        return response()->json($services, 200);
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
            "client" => "required",
            "origin" => "required",
            "destination" => "required",
            "unit" => "required",
            "driver" => "required",
            "assistant" => "required",
            "bands" => "required",
            "roller_skates" => "required",
            "beach" => "required",
            "devils" => "required",
            "mats" => "required",
            "cartons" => "required",
            "charging_hour" => "required",
            "upload_date" => "required",
            "download_date" => "required",
            "download_time" => "required"
        ], [
            "client.required" => "El campo cliente es requerido.",
            "origin.required" => "El campo origen es requerido.",
            "destination.required" => "El campo destino es requerido.",
            "unit.required" => "El campo unidad es requerido.",
            "driver.required" => "El campo operador es requerido.",
            "assistant.required" => "El campo auxiliar es requerido.",
            "bands.required" => "El campo bandas es requerido.",
            "roller_skates.required" => "El campo patines es requerido.",
            "beach.required" => "El campo playos es requerido.",
            "devils.required" => "El campo diablos es requerido.",
            "mats.required" => "El campo colchonetas es requerido.",
            "cartons.required" => "El campo cartones es requerido.",
            "charging_hour.required" => "El campo hora cargue es requerido.",
            "upload_date.required" => "El campo fecha cargue es requerido.",
            "download_date.required" => "El campo fecha descargue es requerido.",
            "download_time.required" => "El campo hora descargue es requerido."
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Service::insert([
                "client_id" => $request["client"],
                "origin_address" => $request["origin"],
                "destination_address" => $request["destination"],
                "unit_id" => $request["unit"],
                "driver_id" => $request["driver"],
                "assistant_id" => $request["assistant"],
                "bands" => $request["bands"],
                "roller_skates" => $request["roller_skates"],
                "beach" => $request["beach"],
                "devils" => $request["devils"],
                "mats" => $request["mats"],
                "cartons" => $request["cartons"],
                "charging_hour" => $request["charging_hour"],
                "upload_date" => $request["upload_date"],
                "download_date" => $request["download_date"],
                "download_time" => $request["download_time"],
                "status_id" => 1
            ]);
            return response()->json(["msg" => "La orden se ha creado de forma exitosa."], 200);
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
        $data = Service::find($id);

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
            "client" => "required",
            "origin" => "required",
            "destination" => "required",
            "unit" => "required",
            "driver" => "required",
            "assistant" => "required",
            "bands" => "required",
            "roller_skates" => "required",
            "beach" => "required",
            "devils" => "required",
            "mats" => "required",
            "cartons" => "required",
            "charging_hour" => "required",
            "upload_date" => "required",
            "download_date" => "required",
            "download_time" => "required"
        ], [
            "client.required" => "El campo cliente es requerido.",
            "origin.required" => "El campo origen es requerido.",
            "destination.required" => "El campo destino es requerido.",
            "unit.required" => "El campo unidad es requerido.",
            "driver.required" => "El campo operador es requerido.",
            "assistant.required" => "El campo auxiliar es requerido.",
            "bands.required" => "El campo bandas es requerido.",
            "roller_skates.required" => "El campo patines es requerido.",
            "beach.required" => "El campo playos es requerido.",
            "devils.required" => "El campo diablos es requerido.",
            "mats.required" => "El campo colchonetas es requerido.",
            "cartons.required" => "El campo cartones es requerido.",
            "charging_hour.required" => "El campo hora cargue es requerido.",
            "upload_date.required" => "El campo fecha cargue es requerido.",
            "download_date.required" => "El campo fecha descargue es requerido.",
            "download_time.required" => "El campo hora descargue es requerido."
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 406);
        }else{
            Service::where("id", $id)->update([
                "client_id" => $request["client"],
                "origin_address" => $request["origin"],
                "destination_address" => $request["destination"],
                "unit_id" => $request["unit"],
                "driver_id" => $request["driver"],
                "assistant_id" => $request["assistant"],
                "bands" => $request["bands"],
                "roller_skates" => $request["roller_skates"],
                "beach" => $request["beach"],
                "devils" => $request["devils"],
                "mats" => $request["mats"],
                "cartons" => $request["cartons"],
                "charging_hour" => $request["charging_hour"],
                "upload_date" => $request["upload_date"],
                "download_date" => $request["download_date"],
                "download_time" => $request["download_time"]
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
        Service::where($id)->delete();

        return response()->json(["msg" => "Se ha elimando el servicio de forma exitosa."], 200);

    }
}
