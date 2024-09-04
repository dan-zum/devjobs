<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacante;
use App\Models\Candidato;
use Illuminate\Auth\Access\Response;

class VacantePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //Ceamos la policy con el rol 2 que sera el reclutador quien tendra el permiso para poder visualizar las vacantes y editarlas 
        return $user->rol === 2;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, vacante $vacante, Candidato $candidato): bool
    {
        //Aqui me quede
        return $user->id === $candidato->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->rol === 2;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, vacante $vacante): bool
    {
        return $user->id === $vacante->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, vacante $vacante): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, vacante $vacante): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, vacante $vacante): bool
    {
        //
    }
}
