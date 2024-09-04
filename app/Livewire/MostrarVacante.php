<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vacante;

class MostrarVacante extends Component
{
    protected $listeners = ['eliminarVacante'];

    public function eliminarVacante(Vacante $vacante){
        dd($vacante->id);
    }

    public function render()
    {
        $vacantes = Vacante::where('user_id', auth()->user()->id)->paginate(6); //Consultar al vacante de acuerdo al reclutante
        return view('livewire.mostrar-vacante',[
            'vacantes' => $vacantes
        ]);
    }
}
