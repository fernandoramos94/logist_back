<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CologneController extends Controller
{
    public function getCodes($code){
        $sql = "SELECT CONVERT(codigo_postal,CHARACTER) as name, CONVERT(codigo_postal,CHARACTER) as id,
        JSON_ARRAYAGG(
            JSON_OBJECT('cologne', nombre, 'stated', ciudad)
        ) as data
        FROM colonias where codigo_postal like '%".$code."%'
        GROUP BY codigo_postal
        limit 1;";

        $dataReturn = DB::select($sql);
        if(count($dataReturn) > 0){
            $dataReturn[0]->data = json_decode($dataReturn[0]->data);
        }

        return response()->json($dataReturn, 200);
    }
}
