<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salario; //Importamos el modelo de Salario
use App\Models\Categoria; //Importamos el modelo de Categorias 
use App\Models\Vacante; //Importamos el modelo de Categorias 
use Livewire\WithFileUploads;
use Illuminate\Support\Carbon;
class EditarVacante extends Component
{
    public $vacante_id;
    public $titulo;
    public $salario;
    public $categoria;
    public $empresa;
    public $ultimo_dia;
    public $descripcion;
    public $imagen;
    public $imagen_nueva;

    use WithFileUploads;

    protected $rules = [
        'titulo' => 'required|string',
        'salario' => 'required',
        'categoria' => 'required',
        'empresa' => 'required',
        'ultimo_dia' => 'required',
        'descripcion' => 'required',
        'imagen_nueva' => 'nullable|image|max:1024'
    ];

    public function mount(Vacante $vacante){
        $this->vacante_id = $vacante->id;
        $this->titulo = $vacante->titulo;
        $this->salario = $vacante->salario_id;
        $this->categoria = $vacante->categoria_id;
        $this->empresa = $vacante->empresa;
        $this->ultimo_dia = Carbon::parse( $vacante->ultimo_dia)->format('Y-m-d');//Paquete incluido en laravel que es util a la hora de manipular fechas
        $this->descripcion = $vacante->descripcion;
        $this->imagen = $vacante->imagen;
    }

    public function EditarVacante()
    {

        $datos = $this->validate();//Validacion de datos de entrada para editar al vacante

        // Si hay una nueva imagen, se subira al servidor
        if($this->imagen_nueva){
            $imagen = $this->imagen_nueva->store('public/vacantes');
            $datos['imagen'] = str_replace('public/vacantes','', $imagen);
        }
        
        //Encontrar la vacante a editar
        $vacante = Vacante::find($this->vacante_id); 

        //Asignar los valores
        $vacante->titulo = $datos['titulo'];
        $vacante->salario_id = $datos['salario'];
        $vacante->categoria_id = $datos['categoria'];
        $vacante->empresa = $datos['empresa'];
        $vacante->ultimo_dia = $datos['ultimo_dia'];
        $vacante->descripcion = $datos['descripcion'];
        $vacante->imagen = $datos['imagen'] ?? $vacante->imagen; //Si el usuario ya subio una nueva imagen $datos['imagen] si no subir la que ya esta en la base de datos $vacante->imagen

        //Guardar la vacante
        $vacante->save();

        //redireccionar
        session()->flash('mensaje','La vacante se actualizo correctamente');

        return redirect()->route('vacantes.index');
    }

    public function render()
    {

        $salarios = Salario::all();
        $categorias = Categoria::all();
        return view('livewire.editar-vacante',[
            'salarios'=>$salarios,
            'categorias'=>$categorias
        ]);
    }
}
