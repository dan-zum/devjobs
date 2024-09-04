<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vacante;
class HomeVacantes extends Component
{
    public $termino;
    public $categoria;
    public $salario;
    protected $listeners = ['buscar' => 'filtrar'];

    public function filtrar($termino,$categoria,$salario){
        
        $this->termino = $termino;
        $this->categoria = $categoria;
        $this->salario = $salario;
       
    }
                                        
    public function render()
    {
        //$vacantes = Vacante::all();
        $vacantes = Vacante::when($this->termino, function($query){
            $query->where('titulo','LIKE',"%" . $this->termino . "%");
        })->when($this->termino, function($query){
            $query->orWhere('empresa', $this->termino);
        })
        ->when($this->categoria, function($query){
            $query->where('categoria_id', $this->categoria);
        })
        ->when($this->salario, function($query){
            $query->where('salario_id', $this->salario);
        })
        ->paginate(5);
        return view('livewire.home-vacantes',[
            'vacantes' => $vacantes
        ]);
    }
}
