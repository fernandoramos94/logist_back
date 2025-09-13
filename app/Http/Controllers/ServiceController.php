<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\Evidences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use App\Models\Status;
use App\Models\AddressService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

        $sql = "SELECT unit.unit, service.*, concat_ws('_', unit.unit, service.unified) as unified_concat, concat_ws(' ', DATE_FORMAT(upload_date, '%d/%m/%Y'), charging_hour) as date_start, concat_ws(' ', DATE_FORMAT(download_date, '%d/%m/%Y'), download_time) as date_end, e.status_id_one, e.status_id_two, client.name as client FROM service 
        INNER JOIN client ON client.id = service.client_id
        INNER JOIN status ON status.id = service.status_id
        INNER JOIN unit on service.unit_id = unit.id
        LEFT JOIN (
            SELECT service_id, 
                SUM(CASE WHEN status_id = 3 THEN 1 ELSE 0 END) AS status_id_one,
                SUM(CASE WHEN status_id = 4 THEN 1 ELSE 0 END) AS status_id_two
            FROM evidences
            GROUP BY service_id
        ) AS e ON e.service_id = service.id
        
         order by service.created_at desc, status.id asc";

        $sqlCount = "SELECT
            SUM(CASE WHEN status_id = 1 THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN status_id in (2,3,4,5)  THEN 1 ELSE 0 END) AS in_route,
            SUM(CASE WHEN status_id = 6 THEN 1 ELSE 0 END) AS good
            FROM service";

        $services = DB::select($sql);
        $servicesCount = DB::select($sqlCount);


        foreach ($services as $item) {
            $item->logs = DB::table("logs")->select("logs.ip", "logs.event", "logs.created_at", "users.name")
                            ->join("users", "users.id", "=", "logs.user_id")
                            ->join("service", "service.id", "=", "logs.service_id")
                            ->where("logs.service_id", $item->id)
                            ->get();

            $item->assistants = DB::table("service_assitant")->where("order_id", $item->id)
            ->join("assistant", "assistant.id", "=", "service_assitant.assistant_id")
            ->get();
        }

        foreach ($services as $item) {
            $item->created_at = Carbon::parse($item->created_at)->format("d/m/Y");
        }

        $gpro = [];

        return response()->json(["data" => $services, "count" => $servicesCount, "data_group" => $gpro], 200);
    }

    public function filter(Request $request){
        // Delegate filtering to the model method
        $result = Service::filterServices($request->all());
        return response()->json([
            "data" => $result['services'],
            "count" => $result['servicesCount'],
            // mantener compatibilidad con la respuesta anterior
            "data_group" => []
        ], 200);
    }

    public function calendar()
    {
        $sql = "SELECT service.*, client.name as title, concat_ws(' ', upload_date, charging_hour) as start, case 
                    when status_id = 1 then '#F53043'
                    when status_id = 2 then '#FCA14E'
                    when status_id = 3 then '#FB8B25'
                    when status_id = 4 then '#57BFFE'
                    when status_id = 5 then '#2F76E5'
                    when status_id = 6 then '#44C150'
                    else '#FF0000' 
                    end as color, '0' as is_end  FROM service inner join client on client.id = service.client_id";
        $sql_end = "SELECT service.*, client.name as title, concat_ws(' ', download_date, download_time) as start, case 
                    when status_id = 1 then '#F53043'
                    when status_id = 2 then '#FCA14E'
                    when status_id = 3 then '#FB8B25'
                    when status_id = 4 then '#57BFFE'
                    when status_id = 5 then '#2F76E5'
                    when status_id = 6 then '#44C150'
                    else '#FF0000' 
                    end as color, '1' as is_end FROM service inner join client on client.id = service.client_id";

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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        } else {
            
            $idService = Service::insertGetId([
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

            $count = Service::all()->count();

            Service::where("id", $idService)->update([
                "folio" => $count, 
                "unified" => isset($request["unified"]) && $request["unified"] != "" ? $request["unified"] : $idService 
            ]);

            $this->insertServiceAssistant($request["assistants"], $idService);
            $this->insertAddress($request["address"], $idService);

            $this->insertLog("Creación de Servicio", $idService, $request["user_id"]);

            return response()->json(["msg" => "La orden se ha creado de forma exitosa."], 200);
        }
    }

    public function unitUpdate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "unit" => "required",
            "driver" => "required",
            "assistant" => "required",
        ], [
            "unit.required" => "El campo unidad es requerido.",
            "driver.required" => "El campo operador es requerido.",
            "assistant.required" => "El campo auxiliar es requerido."
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        } else {
            
            Service::where("id", $request["id"])->update([
                "unit_id" => $request["unit"],
                "driver_id" => $request["driver"],
                "assistant_id" => $request["assistant"],
                "unified" => $request["id"],
                "observation" => $request["observation"],
                "charging_hour" => $request["charging_hour"],
                "upload_date" => $request["upload_date"],
                "download_date" => $request["download_date"],
                "download_time" => $request["download_time"],
                "origin_address" => $request["origin"],
                "destination_address" => $request["destination"]
            ]);

            DB::table("service_assitant")->where("order_id", $request["id"])->delete();

            $this->insertServiceAssistant($request["assistants"], $request["id"]);

            $this->insertLog("Cambio de unidad", $request["id"], $request["user_id"]);

            return response()->json(["msg" => "Se ha realizado el cambio de unidad de forma exitosa."], 200);
        }
    }

    public function insertServiceAssistant($data, $service_id){
        $arrayDataInsert = [];
        foreach ($data as $item) {
            $arrayDataInsert[] = array("order_id" => $service_id, "assistant_id" => $item["assistant_id"]);
        }
        $table = DB::table("service_assitant")->insert($arrayDataInsert);
        return true;
    }

    public function insertAddress($data, $service_id){
        $arrayDataInsert = [];
        foreach ($data as $item) {
            $arrayDataInsert[] = array("service_id" => $service_id, "status_id" => 1, "origin" => $item["origin"], "destination" => $item["destination"]);
        }

        AddressService::insert($arrayDataInsert);
        // $table = DB::table("service_address")->insert($arrayDataInsert);
        return true;
    }

    public function insertLog($event, $service_id, $user_id){
        $insert = DB::table("logs")->insert([
            "user_id" => $user_id,
            "event" => $event,
            "service_id" => $service_id,
            "ip" => file_get_contents('https://api.ipify.org'),
            "created_at" => Carbon::now()
        ]);

        return true;
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        } else {
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

    public function uploadimage(Request $request, $id, $status, $user_id, $next_status, $address_service_id, $next_address_service_id, $indexAddressCurrent)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['upload_file_not_found'], 400);
        }

        $allowedfileExtension = ['jpg', 'png', 'jpeg', 'gif', 'svg'];
        $files = $request->file('file');
        $errors = [];

        foreach ($files as $file) {

            $extension = $file->getClientOriginalExtension();

            $check = in_array($extension, $allowedfileExtension);

            if ($check) {
                foreach ($files as $mediaFiles) {

                    $path = $mediaFiles->store('public/images');
                    $name = $mediaFiles->getClientOriginalName();

                    //store image file into directory and db
                    $save = new Evidences();
                    
                    $save->img = $path;
                    $save->service_id = $id;
                    $save->status_id = $status;
                    $save->identifier = $indexAddressCurrent;
                    $save->save();

                    $text_status = "";

                    if((int)$status === 3){
                        $text_status = "Carga";
                    }else{
                        $text_status = "Descarga";
                    }

                    $this->insertLog("Cargue Evidencias de ". $text_status, $id, $user_id);


                    if($address_service_id != null){
                        if((int)$next_status != 0){
                            if((int)$status == 8){
                                AddressService::where("id", $address_service_id)->update(["status_id" => $next_status]);
                                if($next_address_service_id != null){
                                    AddressService::where("id", $next_address_service_id)->update(["status_id" => 4]);
                                }
                            }
                        }
                    }else{
                        if((int)$next_status != 0){
                            $status_find = Status::find($next_status);
                            $this->insertLog("Cambio de Estado: ". $status_find->name, $id, $user_id);
                            Service::where("id", $id)->update(["status_id" => $next_status]);
                        }
                    }
                }
            } else {
                return response()->json(['invalid_file_format'], 422);
            }

            return response()->json(['file_uploaded'], 200);
        }
    }
    public function updateStatus($id, $status, $user_id, $address_service_id, $next_address_service_id)
    {
        // print_r(gettype($address_service_id));
        if($address_service_id == null || $address_service_id == "null" || $address_service_id == "undefined" || $address_service_id == ""){
            Service::where("id", $id)->update(["status_id" => $status]);
        } else {
            AddressService::where("id", $address_service_id)->update(["status_id" => $status]);
        }
        $statusData = Status::find($status);
        $this->insertLog("Cambio de Estado: ". $statusData->name, $id, $user_id);

        return response()->json(["msg" => "ok"], 200);
    }

    public function generatePDF($id)
    {

        $info = Service::select(
            "service.*",
            "client.name as client",
            "unit.plates",
            "unit.unit",
            "unit.type",
            DB::raw("CONCAT(driver.name, ' ', driver.last_name) AS driver"),
            DB::raw("CONCAT(assistant.name, ' ', assistant.last_name) AS assistant")
        )->join("client", "client.id", "=", "service.client_id")
            ->join("unit", "unit.id", "=", "service.unit_id")
            ->join("assistant", "assistant.id", "=", "service.assistant_id")
            ->join("driver", "driver.id", "=", "service.driver_id")
            ->where("service.id", $id)->first();


        $info->upload_date = date('m/d/Y', strtotime($info->upload_date));
        $info->download_date = date('m/d/Y', strtotime($info->download_date));


        $assistants = DB::table("service_assitant")->select(DB::raw("CONCAT(assistant.name, ' ', assistant.last_name) AS assistant"))->where("order_id", $id)
            ->join("assistant", "assistant.id", "=", "service_assitant.assistant_id")
            ->get();

        $address = DB::table("service_address")->select("destination", "origin")->where("service_id", $id)
            ->get();

        foreach ($assistants as $item) {
            $info->assistant = $info->assistant . ', '.  $item->assistant;
        }

        foreach ($address as $item) {
            $info->origin_address = $info->origin_address . " || " .  $item->origin;
            $info->destination_address = $info->destination_address . ' || '.  $item->destination;
        }
        $data = [
            'title' => 'Services',
            'data' => $info
        ];

        $pdf = PDF::loadView('orden', $data);

        return $pdf->stream('ordenServicio' . $info->id . '.pdf');
    }

    public function observation(Request $request, $id){
        Service::where("id", $id)->update(["observation" => $request["observation"]]);
        return response()->json(["msg" => "ok"], 200);
    }

    public function evidences(Request $request)
    {
        $data = Evidences::where([
            ["service_id", "=", $request["orden_id"]],
            ["status_id", "=", $request["status_id"]]
        ])->get();


        foreach ($data as $item) {
            $item["img"] = Storage::url($item["img"]);
        }

        return response()->json($data, 200);
    }

    public function allEvidences(Request $request){
        $data = Evidences::where([
            ["service_id", "=", $request["orden_id"]]
        ])->get();
        
        foreach ($data as $item) {
            $item["img"] = Storage::url($item["img"]);
        }

        return response()->json($data, 200);
    }

    public function cancelOrder($id, $user_id){
        $this->insertLog("Cancelación del Servicio", $id, $user_id);

        Service::where("id", $id)->update(["status_id" => 7]);
        return response()->json(["msg" => "ok"], 200);
    }
    public function validUnified(Request $request) {

        $info = Service::select(
            "service.id",
            "service.driver_id",
            "service.assistant_id",
            DB::raw("CONCAT(driver.name, ' ', driver.last_name) AS driver"),
            DB::raw("CONCAT(assistant.name, ' ', assistant.last_name) AS assistant")
        )->join("client", "client.id", "=", "service.client_id")
            ->join("unit", "unit.id", "=", "service.unit_id")
            ->join("assistant", "assistant.id", "=", "service.assistant_id")
            ->join("driver", "driver.id", "=", "service.driver_id")
            ->where([
                ["service.unit_id", "=", $request["unit"]],
                ["service.destination_address", "=", $request["destination"]],
            ])->whereDate('service.created_at', DB::raw('CURDATE()'))->first();


        return response()->json(["data" => $info]);
        
    }
}