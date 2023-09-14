<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function getData() {
        $data = Status::all();
        return response()->json($data, 200);
    }
}
