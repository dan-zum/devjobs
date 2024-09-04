<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salario;
use App\Models\Categoria;

class BuscadorVacantes extends Component
{

    public $termino;
    public $categoria;
    public $salario;

    public function Buscar(){
        //Buscando a livewire huijo para pasarle los datos
        $this->dispatch('buscar',$this->termino,$this->categoria,$this->salario);
    }
   /* public function buscar(){
        dd($this->resultados);
        return;
    }*/

    public function render()
    {
       
        $salarios = Salario::all();
        $categorias = Categoria::all();

        return view('livewire.buscador-vacantes',[
            'salarios' => $salarios,
            'categorias' => $categorias
        ]);
    }
}
