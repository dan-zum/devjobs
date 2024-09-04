<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacante;
use App\Models\Candidato;
class VacanteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $this->authorize('viewAny', Vacante::class);//Esto va de la mano con las policies
       

        return view('vacantes.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Vacante::class);
        return view('vacantes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Vacante $vacante)
    {
       
         //$candidatos = Candidato::where('vacante_id',$vacante->id)->get();
        
        return view('vacantes.candidatos',[
            "vacante" => $vacante
        ]);
    }

    /*public function ejemplo(){
        return view('plantilla.correo');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vacante $vacante)
    {
        return view('vacantes.show',[
            'vacante' => $vacante
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacante $vacante)
    {
       $this->authorize('update', $vacante); //Nombre del policy, en este caso en update

        return view('vacantes.edit', [
            'vacante'  => $vacante
        ]);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
