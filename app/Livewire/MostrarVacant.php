<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Candidato;
class MostrarVacant extends Component
{
    //Definiendo la vacante
    public $vacante;
   
    
    public function render()
    {
      
        return view('livewire.mostrar-vacant');
    }
}
