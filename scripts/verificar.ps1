# Verificación manual del despliegue de Laravel desde Windows (PowerShell)
# Uso:  .\scripts\verificar.ps1

$url = "http://localhost:8081"

Write-Host "== Contenedores ==" -ForegroundColor Cyan
docker ps --filter "name=laravel-" --format "table {{.Names}}\t{{.Image}}\t{{.Status}}\t{{.Ports}}"

Write-Host "`n== Estado de salud ==" -ForegroundColor Cyan
foreach ($c in @("laravel-web", "laravel-db")) {
    $estado = docker inspect --format "{{.State.Health.Status}}" $c 2>$null
    Write-Host "$c : $estado"
}

Write-Host "`n== Health check HTTP ==" -ForegroundColor Cyan
try {
    $r = Invoke-RestMethod -Uri "$url/health" -TimeoutSec 5
    Write-Host "Respuesta: $($r | ConvertTo-Json -Compress)" -ForegroundColor Green
} catch {
    Write-Host "No responde $url/health  -> $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n== Logs de Nginx ==" -ForegroundColor Cyan
docker logs --tail 10 laravel-web

Write-Host "`n== Logs de Laravel (PHP-FPM) ==" -ForegroundColor Cyan
docker logs --tail 10 laravel-app
