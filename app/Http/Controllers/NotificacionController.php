<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $notificaiones = auth()->user()->unreadNotifications()->get();//Traer todas las notificaciones

        auth()->user()->unreadNotifications->markAsRead();//Limpiar las notificaciones una vez que actualiza la vista de notificacines el reclutador

        return view('notificaciones.index',[
            'notificaciones' => $notificaiones
        ]);
    }
}
