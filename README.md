# DevJobs — Bolsa de empleo

Plataforma de empleo tipo Indeed donde los reclutadores publican vacantes y los candidatos las buscan y se postulan enviando su CV. El reclutador recibe una notificación por cada nuevo candidato y puede revisar las postulaciones de cada vacante.

## Tecnologías

- Laravel
- Laravel Breeze (autenticación)
- Livewire
- Tailwind CSS
- MySQL

## Funciones

### Para reclutadores
- **Publicación de vacantes** con título, salario, categoría, empresa, fecha límite, descripción e imagen.
- **Edición y eliminación** de sus propias vacantes.
- **Notificaciones** por correo y dentro de la plataforma cuando un candidato se postula.
- **Lista de candidatos** por vacante, con acceso a su CV.

### Para candidatos
- **Buscador de vacantes** con filtros por término, categoría y salario.
- **Postulación** a una vacante subiendo su CV.

### General
- **Registro e inicio de sesión** con Laravel Breeze, incluida la verificación de correo electrónico.
- **Roles de usuario** (reclutador y candidato).

## Decisiones técnicas

- **Componentes de Livewire** para crear, editar, mostrar y buscar vacantes, y para postularse, sin recargar la página y sin escribir JavaScript adicional.
- **Carga de archivos con Livewire** (`WithFileUploads`) para las imágenes de las vacantes y los CV de los candidatos.
- **Middleware propio (`rol.reclutador`)** que restringe el panel de vacantes y las notificaciones a los reclutadores.
- **Policies (`VacantePolicy`)** para que cada reclutador solo pueda editar o eliminar sus propias vacantes.
- **Notificaciones de Laravel** (`NuevoCandidato`) enviadas por dos canales: correo y base de datos.
- **Filtros con `when()` de Eloquent** para construir la búsqueda solo con los criterios que el usuario elige.

## Base de datos

La estructura se crea con **migraciones** y los catálogos de categorías y rangos de salario con **seeders**, así que no se necesita importar ningún archivo `.sql`.

## Instalación

1. Clona el repositorio:
   ```bash
   git clone https://github.com/dan-zum/devjobs.git
   cd devjobs
   ```
2. Instala las dependencias de PHP y de JavaScript:
   ```bash
   composer install
   npm install
   ```
3. Copia `.env.example` a `.env` y configura tu base de datos y tu servidor de correo (por ejemplo, Mailtrap para pruebas):
   ```bash
   cp .env.example .env
   ```
4. Genera la clave de la aplicación:
   ```bash
   php artisan key:generate
   ```
5. Crea las tablas y carga los catálogos:
   ```bash
   php artisan migrate --seed
   ```
6. Crea el enlace para que se vean las imágenes y los CV:
   ```bash
   php artisan storage:link
   ```
7. Compila los estilos e inicia el servidor (en dos terminales):
   ```bash
   npm run dev
   php artisan serve
   ```

La aplicación queda disponible en `http://localhost:8000`.

## Autor

**Daniel Vazquez Zumaya** — Desarrollador Web Full Stack
[LinkedIn](https://www.linkedin.com/in/danielvazquezzumaya/) · [Portafolio](https://daniel.mictlancode.com)
