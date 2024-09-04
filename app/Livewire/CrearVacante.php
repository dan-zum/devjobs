<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Salario; //Importamos el modelo de Salario
use App\Models\Categoria; //Importamos el modelo de Categorias
use App\Models\Vacante;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Recordatorio;
use App\Models\Candidato;
use Livewire\WithFileUploads;
class CrearVacante extends Component
{

    public $vacante;
    public $titulo;
    public $salario;
    public $categoria;
    public $empresa;
    public $ultimo_dia;
    public $descripcion;
    public $imagen;
    public $arreglo = [];
    public $vacanteId = [];
    public $resultados_total = [];
    public $usuarios = [];

    protected $rules = [
        'titulo' => 'required|string',
        'salario' => 'required',
        'categoria' => 'required',
        'empresa' => 'required',
        'ultimo_dia' => 'required',
        'descripcion' => 'required',
        'imagen' => 'required|image|max:1020',
        'arreglo.*' => 'required'
    ];


    use WithFileUploads; //De esta forma le decimos a livewire que queremos subir archivos
    
    
   
    public function mount(){ //en Laravel Livewire se utiliza para inicializar componentes con datos específicos antes de que se rendericen en la vista.

        
        $this->resultados_total = $resultados = Candidato::all();
        //$this->CNombre = $this->categoria;
        foreach($resultados as $resultado){
            //$this->arreglo[$resultado->email] = $resultado->email;
            //$nuevo = explode('.com',$resultado->email);
            //dd($nuevo);
            $this->arreglo[$resultado->user_id] = $resultado->user_id;
            $this->vacanteId[$resultado->vacante_id] = $resultado->vacante_id;
            //dd($resultado);
        }
        
       
    }

    public function crearVacante(){

        //Consulta de categoria
        $consulta_categoria = Categoria::where('id', $this->categoria)->get();

    
        foreach($consulta_categoria as $key => $valor){
            //Comparacion para saber si el reclutador que esta postulando la vacante coincida con alguna de la base de datos
               $categori = $valor['categoria'];
        }
        
        //Envio de correo electronico 
        $detalle = $categori;//La categoria en una variable
       
        
        foreach($this->resultados_total as $key=>$value){
          
            if(trim(strval($value['vacante_id'])) == trim(strval($this->categoria))){//trim elimina los espacios
               // dd($value['user_id']);
                $this->usuarios[$value['user_id']] = $value['user_id'];
              
            }

        }
        
       
        /*foreach($resultados as $key=>$value){
            if(trim(strval($value['vacante_id'])) === trim(strval($this->categoria))){//trim elimina los espacios
                dd('hay una coincidencia');
            }else{
                dd('No hay coincidencia');
            }
        }*/
        
        
        //Consulta de usuarios
        $consulta_correos = User::whereIn('id', $this->usuarios)->get();
      
        //A los correos que se enviaran
        foreach($consulta_correos as $value){

            
            Mail::to($value['email'])->send(new Recordatorio($detalle,$value['name']));
            //session()->flash('message', 'Correo enviado');
        }
    
        
       
       $datos = $this->validate();//El dato ya tiene el request validado, ya solo lo pasamos

       $imagen = $this->imagen->store('public/vacantes');//Guardando en el servidor

      
       $datos['imagen'] = str_replace('public/vacantes','',$imagen);

    
       //Crear Vacante
       Vacante::create([
        'titulo' =>$datos['titulo'],
        'salario_id' =>$datos['salario'],
        'categoria_id' =>$datos['categoria'],
        'empresa' =>$datos['empresa'],
        'ultimo_dia' =>$datos['ultimo_dia'],
        'descripcion' =>$datos['descripcion'],
        'imagen' =>$datos['imagen'],
        'user_id' =>auth()->user()->id
       ]);
    
       //Crear un mensaje para el usuario
       session()->flash('mensaje','La vacante se publico correctamente');
       //Redireccionar al usuario
       return redirect()->route('vacantes.index');
    }

    public function render()
    {
        $salarios = Salario::all();
        $categorias = Categoria::all();
        $candidatos = Candidato::all();
        $vacantes = Vacante::all();
        $usuarios = User::all();

        return view('livewire.crear-vacante', [
            'salarios' => $salarios,
            'categorias'=>$categorias,
            'candidatos'=>$candidatos,
            'vacantes'=>$vacantes,
            'usuarios'=>$usuarios
        ]);
    }
}
