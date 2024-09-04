import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

console.log('Recibiendo respuesta');
  
    document.addEventListener('livewire:load', function(){
    var mensaje = document.querySelector('.mensaje');
    console.log('Recibiendo respuesta');

    console.log(mensaje)
})

