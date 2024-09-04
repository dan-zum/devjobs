<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vacante;
use App\Models\Candidato;
class MostrarCandidatos extends Component
{


    public $vacante;
    public $candidato;
 
    public function mount(Vacante $vacante){
        $this->vacante = $vacante;
       
        
    }

    public function render()
    {
         //$vacantes = Candidato::where('vacante_id', $this->vacante->id);
        dd($this->vacante->candidatos);
        //$Candidatos = Candidato::where('');
        return view('livewire.mostrar-candidatos');
    }
}
