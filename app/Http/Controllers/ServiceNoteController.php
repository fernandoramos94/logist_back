<?php

namespace App\Http\Controllers;

use App\Models\ServiceNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ServiceNoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $serviceNotes = ServiceNote::all();
        return response()->json($serviceNotes);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $serviceNotes = ServiceNote::all();
        return response()->json($serviceNotes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "service_id" => "required",
            "note" => "required"
        ], [
            "user_id.required" => "El usuario es requerido.",
            "service_id.required" => "El servicio es requerido.",
            "note.required" => "El campo nota es requerida."
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 406);
        }
        $serviceNote = ServiceNote::create($request->all());
        return response()->json(["msg" => "Datos actualizado de forma existosa.", "data" => $serviceNote], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ServiceNote $serviceNote)
    {
        $serviceNote = ServiceNote::find($serviceNote->id);
        return response()->json($serviceNote);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ServiceNote $serviceNote)
    {
        $serviceNote = ServiceNote::find($serviceNote->id);
        return response()->json($serviceNote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ServiceNote $serviceNote)
    {
        $serviceNote = ServiceNote::find($serviceNote->id);
        $serviceNote->update($request->all());
        return response()->json($serviceNote);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ServiceNote $serviceNote)
    {
        $serviceNote = ServiceNote::find($serviceNote->id);
        $serviceNote->delete();
        return response()->json($serviceNote);
    }
}
