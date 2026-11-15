# Pipeline DevOps para Laravel 9 (Windows 11)

**Laravel 9.52 · PHP 8.2 · Composer 2.2 · MySQL 8 · Vite**

```
Git/GitHub → Jenkins (composer + npm + PHPUnit) → SonarQube (Quality Gate)
   → Docker (imagen PHP-FPM + imagen Nginx) → Docker Hub
   → Despliegue: Nginx + PHP-FPM + MySQL → Verificación (/health + logs)
```

## Arquitectura del despliegue

```
Navegador ──► localhost:8081 ──► [laravel-web]  Nginx (archivos estáticos + assets de Vite)
                                      │ fastcgi :9000
                                      ▼
                                 [laravel-app]  PHP-FPM 8.2 (Laravel)
                                      │ :3306
                                      ▼
                                 [laravel-db]   MySQL 8.0 (volumen mysql_data)
```

## Qué archivos copiar a tu proyecto

Copia todo el contenido de esta carpeta a la **raíz de tu proyecto Laravel** (donde está `artisan`):

| Archivo | Para qué sirve |
|---|---|
| `Dockerfile` | Construye 2 imágenes: `app` (PHP-FPM) y `web` (Nginx) |
| `docker/nginx/default.conf` | Nginx → PHP-FPM, raíz en `public/` |
| `docker/php/php.ini` | Ajustes de PHP y OPcache para producción |
| `docker/php/entrypoint.sh` | Cachea config, rutas y vistas al arrancar |
| `docker-compose.prod.yml` | Despliegue: `web` + `app` + `db` |
| `.env.prod.example` | Plantilla del `.env` de producción (va a Jenkins, **no** a Git) |
| `.dockerignore` | Evita meter `vendor`, `node_modules`, `.env` en la imagen |
| `.gitattributes` | Mantiene `.sh` con saltos de línea LF (importante en Windows) |
| `sonar-project.properties` | Configuración del análisis de SonarQube |
| `Jenkinsfile` | Pipeline de 10 etapas |
| `tests/Feature/HealthCheckTest.php` | Prueba del endpoint `/health` |
| `infra/` | Jenkins + SonarQube (docker-compose) |
| `scripts/verificar.ps1` | Verificación manual desde PowerShell |

### Cambios que debes hacer tú en el código

**1. Agrega la ruta de health check** al final de `routes/web.php` (Laravel 9 no trae `/up`):

```php
use Illuminate\Support\Facades\DB;

Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'UP', 'database' => 'UP']);
    } catch (\Throwable $e) {
        return response()->json(['status' => 'DOWN', 'database' => 'DOWN'], 503);
    }
});
```

**2. Agrega a tu `.gitignore`:**

```
.env.prod
reports/
.scannerwork/
```

**3. Comprueba que tienes `composer.lock` y `package-lock.json` versionados** (`git ls-files composer.lock package-lock.json`). Si no, genera los archivos con `composer install` y `npm install`, y súbelos.

**4. Prueba local** (en tu PC):

```powershell
php artisan test
npm run build
```

---

## Requisitos (Windows 11)

- **WSL 2** (`wsl --install` en PowerShell como administrador).
- **Docker Desktop** con *WSL 2 based engine*. En *Settings → Resources* asigna al menos **6 GB de RAM** (Jenkins + SonarQube + MySQL).
- **Git for Windows**, y cuentas en **GitHub** y **Docker Hub**.

---

## Paso 1-3. Proyecto, Git y GitHub

Tu proyecto ya está versionado, solo sube los archivos nuevos:

```powershell
git add .
git commit -m "ci: pipeline Jenkins + SonarQube + Docker para Laravel"
git push
```

Si quieres mostrar el flujo con ramas y PR (opcional):

```powershell
git checkout -b feature/devops
git add . && git commit -m "ci: agregar pipeline"
git push -u origin feature/devops
# En GitHub: Pull Request feature/devops → main (o master) y Merge
```

## Paso 4. Levantar Jenkins y SonarQube

```powershell
cd infra
docker compose up -d --build
```

La primera vez tarda (descarga PHP, Node, plugins…).

- Jenkins: http://localhost:8080 → contraseña inicial:
  `docker exec jenkins cat /var/jenkins_home/secrets/initialAdminPassword`
  → *Install suggested plugins* → crea tu usuario.
- SonarQube: http://localhost:9000 → `admin` / `admin` → cambia la contraseña.

Comprueba que Jenkins tiene las herramientas:

```powershell
docker exec jenkins php -v
docker exec jenkins composer --version
docker exec jenkins node -v
docker exec jenkins docker compose version
```

> Si SonarQube se reinicia constantemente:
> `wsl -d docker-desktop sysctl -w vm.max_map_count=262144` y `docker restart sonarqube`.

## Paso 5. Configurar las conexiones

**5.1 Token de SonarQube:** *My Account → Security → Generate Token* (tipo *Global Analysis*).

**5.2 Webhook SonarQube → Jenkins:** *Administration → Configuration → Webhooks → Create*
- URL: `http://jenkins:8080/sonarqube-webhook/` (con la `/` final)

**5.3 Credenciales en Jenkins** (*Manage Jenkins → Credentials → System → Global → Add*):

| ID | Tipo | Contenido |
|---|---|---|
| `sonar-token` | Secret text | Token de SonarQube |
| `dockerhub-creds` | Username with password | Usuario de Docker Hub + Access Token |
| `laravel-env-prod` | **Secret file** | Tu `.env.prod` (ver abajo) |
| `github-creds` | Username with password | Solo si el repo es privado (usuario + Personal Access Token) |

**Cómo crear el `.env.prod`:** copia `.env.prod.example` a un archivo `.env.prod` **fuera del repo**, cambia las contraseñas y genera la `APP_KEY`:

```powershell
php artisan key:generate --show
```

Pega el resultado en `APP_KEY=` y sube ese archivo como credencial *Secret file*.

**5.4 Servidor SonarQube:** *Manage Jenkins → System → SonarQube servers* → marca *Environment variables* → Add:
- Name: `SonarQube` · URL: `http://sonarqube:9000` · Token: `sonar-token`

**5.5 Scanner:** *Manage Jenkins → Tools → SonarQube Scanner installations → Add*:
- Name: `SonarScanner` · *Install automatically*

## Paso 6. Jenkinsfile y job

Edita en el `Jenkinsfile`:

```groovy
DOCKERHUB_USER = 'tu-usuario-dockerhub'
```

Haz commit y push. Luego en Jenkins: **New Item** → `laravel-app` → **Pipeline**:
- Definition: *Pipeline script from SCM* → Git → URL de tu repo.
- Branch Specifier: `*/main` (o `*/master`, según tu rama, que puedes ver con `git branch`).
- Script Path: `Jenkinsfile`.
- (Opcional) *Poll SCM*: `H/5 * * * *`.

| # | Etapa | Qué hace |
|---|---|---|
| 1 | Checkout | Clona el repo |
| 2 | Dependencias | `composer install` + `npm ci` |
| 3 | Compilar assets | `npm run build` (Vite) |
| 4 | Pruebas | PHPUnit con SQLite en memoria + cobertura (PCOV) |
| 5 | SonarQube | Análisis de código + cobertura |
| 6 | Quality Gate | Detiene el pipeline si no pasa |
| 7 | Construir imágenes | `laravel-app` (PHP-FPM) y `laravel-web` (Nginx) |
| 8 | Publicar | `docker push` a Docker Hub |
| 9 | Desplegar | `docker compose up -d` + `php artisan migrate --force` |
| 10 | Verificación | `/health`, estado del HEALTHCHECK y `docker logs` |

## Paso 7. Ejecutar pruebas y análisis

**Build Now**. Revisa *Test Result* en Jenkins y el proyecto `laravel-app` en SonarQube.

> **Sobre las pruebas:** se ejecutan con SQLite en memoria (rápido, sin MySQL). Si alguna de tus migraciones usa algo exclusivo de MySQL (p. ej. `->change()` sin `doctrine/dbal`, `ENUM` modificados, SQL crudo), esa prueba fallará. Puedes ajustar la migración o pedirme la variante con MySQL de pruebas.

> **Sobre el Quality Gate:** el perfil *Sonar way* exige 80 % de cobertura en el código **nuevo**. Si tu proyecto tiene pocas pruebas, puedes crear en SonarQube un Quality Gate más flexible (*Quality Gates → Create*) y asignarlo al proyecto.

## Paso 8-9. Imágenes y despliegue

Lo hacen las etapas 7 a 9. En tu PC:

```powershell
docker images "*laravel*"
docker ps --filter name=laravel-
```

## Paso 10. Comprobar que funciona

- App: **http://localhost:8081**
- Health: **http://localhost:8081/health** → `{"status":"UP","database":"UP"}`

```powershell
.\scripts\verificar.ps1
docker logs -f laravel-app     # logs de Laravel (LOG_CHANNEL=stderr)
docker logs -f laravel-web     # logs de Nginx
docker exec -it laravel-app php artisan migrate:status
```

---

## Solución de problemas

| Problema | Solución |
|---|---|
| `entrypoint: not found` o `/bin/sh^M` | El `.sh` tiene finales CRLF: verifica `.gitattributes`, luego `git rm --cached -r . && git reset --hard` |
| 502 Bad Gateway | `laravel-app` no arrancó: `docker logs laravel-app` (normalmente `APP_KEY` vacía o error en `.env.prod`) |
| `SQLSTATE[HY000] [2002]` | MySQL aún arrancando o `DB_HOST` distinto de `db` |
| Estilos no cargan | Falta `npm run build` o `@vite` apunta a otro archivo; revisa `public/build/manifest.json` en la imagen |
| `route:cache omitido` en logs | Tienes rutas duplicadas por nombre; no es grave |
| Quality Gate esperando | Revisa el webhook del paso 5.2 |
| Puerto 8081 ocupado | Cambia `"8081:80"` en `docker-compose.prod.yml` |

## Despliegue manual (sin Jenkins)

```powershell
docker network create devops-net   # si no existe
docker build --target app -t tu-usuario/laravel-app:latest .
docker build --target web -t tu-usuario/laravel-web:latest .
$env:DOCKERHUB_USER="tu-usuario"
docker compose -f docker-compose.prod.yml --env-file .env.prod up -d
docker compose -f docker-compose.prod.yml --env-file .env.prod exec app php artisan migrate --force
```

## Apagar

```powershell
docker compose -f docker-compose.prod.yml --env-file .env.prod down      # conserva la BD
cd infra; docker compose down
```

> Jenkins como root con acceso a `docker.sock` es adecuado para un laboratorio local, no para producción.
