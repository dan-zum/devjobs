<?php

namespace App\Livewire;

use Livewire\Component;
use App\Notifications\NuevoCandidato;
use Livewire\WithFileUploads;
use App\Models\Vacante;
use App\Models\Candidato;
class PostularVacante extends Component
{
    public $consulta;
    public $cv;
    public $vacante;
    protected $rules = [
        "cv" => "required|mimes:pdf"
    ]; 

    use WithFileUploads;

    public function mount(Vacante $vacante){
        $this->vacante = $vacante;
    }

    public function postularme(){
        if(auth()->user()){
            $consulta = Candidato::where('user_id', auth()->user()->id)
                ->where('vacante_id', $this->vacante->id)->get();

        

            if($consulta->count()){
                session()->flash('mensaje','Ya te postulaste a esta vacante, espera a la respuesta, gracias!!');
                return redirect()->back();
            }else{
            
                $datos = $this->validate();
        
                $cv = $this->cv->store('public/cv_vacantes');
        
                $datos['cv'] = str_replace('public/cv_vacantes','',$cv);
        
                //Guardar el candidato
                Candidato::create([
                    'user_id' => auth()->user()->id,
                    'vacante_id' => $this->vacante->id,
                    'cv' => $datos['cv']
                ]);
        
                //Crear notificacion y enviar el email
                $this->vacante->reclutador->notify(new NuevoCandidato($this->vacante->id,$this->vacante->titulo,auth()->user()->id));
                //Nota: Al enviar la notificacion al reclutador de que tiene un vacante, se reliza un relacion en el modelo de Vacante
                //Para traerme el id del reclutador en la tabla de vacantes, u justo en la linea de la notificacion, parasle el id (reclutador)
        
                session()->flash('mensaje','Se ha publicado tu postulacion a la vacante');
        
                return redirect()->back();
            }
        }else{
            session()->flash('mensaje_error','Necesitas tener una cuenta para aplicar a esta vacante');
            return redirect()->back();
        }
 
    }

    

    public function render()
    {
        return view('livewire.postular-vacante');
    }
}
