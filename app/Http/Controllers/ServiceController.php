<?php

namespace App\Http\Controllers;

use App\Models\Evidences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $sql = "SELECT service.*, e.status_id_one, e.status_id_two, client.name as client FROM service
        INNER JOIN client ON client.id = service.client_id
        LEFT JOIN (
            SELECT service_id, 
                SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) AS status_id_one,
                SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS status_id_two
            FROM evidences
            GROUP BY service_id
        ) AS e ON e.service_id = service.id";

        $sqlCount = "SELECT
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status_id in (2,3,4,5)  THEN 1 ELSE 0 END) AS in_route,
            SUM(CASE WHEN status_id = 6 THEN 1 ELSE 0 END) AS good
            FROM service where created_at like '%".Carbon::now()->toDateString()."%'";

        $services = DB::select($sql);
        $servicesCount = DB::select($sqlCount);

        return response()->json(["data" => $services, "count" => $servicesCount], 200);
    }

    public function calendar(){
        $sql = "SELECT service.*, client.name as title, concat_ws(' ', upload_date, charging_hour) as start, '#008f39' as color, '0' as is_end  FROM service inner join client on client.id = service.client_id";
        $sql_end = "SELECT service.*, client.name as title, concat_ws(' ', download_date, download_time) as start, '#cc0000' as color, '1' as is_end FROM service inner join client on client.id = service.client_id";

        $data = DB::select($sql);
        $data2 = DB::select($sql_end);

        $result = array_merge($data, $data2);

        return response()->json($result, 200);
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
                "status_id" => 1,
                "created_at" => Carbon::now()
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

    public function uploadimage(Request $request, $id, $status)
    {
        if(!$request->hasFile('file')) {
            return response()->json(['upload_file_not_found'], 400);
        }
     
        $allowedfileExtension=['jpg','png','jpeg','gif','svg'];
        $files = $request->file('file'); 
        $errors = [];
     
        foreach ($files as $file) {      
     
            $extension = $file->getClientOriginalExtension();
     
            $check = in_array($extension,$allowedfileExtension);
     
            if($check) {
                foreach($files as $mediaFiles) {
     
                    $path = $mediaFiles->store('public/images');
                    $name = $mediaFiles->getClientOriginalName();
          
                    //store image file into directory and db
                    $save = new Evidences();
                    $save->img = $path;
                    $save->service_id = $id;
                    $save->status_id = $status;
                    $save->save();
                    if((int)$status == 3){
                        Service::where("id", $id)->update(["status_id" => 4]);
                    }else if((int)$status == 4){
                        Service::where("id", $id)->update(["status_id" => 5]);
                    }
                }
            } else {
                return response()->json(['invalid_file_format'], 422);
            }
     
            return response()->json(['file_uploaded'], 200);
     
        }
    }
    public function updateStatus($id, $status){
        Service::where("id", $id)->update(["status_id" => $status]);

        return response()->json(["msg" => "ok"], 200);
    }

    public function generatePDF($id){

        $info = Service::select(
            "service.*",
            "client.name as client",
            "unit.plates",
            "unit.unit",
            DB::raw("CONCAT(driver.name, ' ', driver.last_name) AS driver"),
            DB::raw("CONCAT(assistant.name, ' ', assistant.last_name) AS assistant")
        )->join("client", "client.id", "=", "service.client_id")
        ->join("unit", "unit.id", "=", "service.unit_id")
        ->join("assistant", "assistant.id", "=", "service.assistant_id")
        ->join("driver", "driver.id", "=", "service.driver_id")
        ->where("service.id", $id)->first();
        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'data' => $info
        ]; 

            

        $pdf = PDF::loadView('orden', $data);

        return $pdf->stream('ordenServicio'.$info->id.'.pdf');
    }
}
