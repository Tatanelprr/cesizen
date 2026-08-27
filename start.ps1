# CESIZen - Script de démarrage Windows (PowerShell)

$ErrorActionPreference = "Stop"
$projectPath = Split-Path -Parent $MyInvocation.MyCommand.Path

Write-Host ""
Write-Host "  CESI" -ForegroundColor Green -NoNewline
Write-Host "Zen" -ForegroundColor Yellow -NoNewline
Write-Host " - Demarrage de l'application" -ForegroundColor White
Write-Host "  ----------------------------------------" -ForegroundColor DarkGray
Write-Host ""

# Aller dans le dossier du projet
Set-Location $projectPath

# ── Vérification PHP ─────────────────────────────────────────────────────────
Write-Host "[1/5] Verification PHP..." -ForegroundColor Cyan -NoNewline
try {
    $phpVersion = (php -r "echo PHP_VERSION;") 2>$null
    Write-Host " OK (PHP $phpVersion)" -ForegroundColor Green
} catch {
    Write-Host " ERREUR" -ForegroundColor Red
    Write-Host "      PHP n'est pas installe ou pas dans le PATH." -ForegroundColor Red
    Write-Host "      Telechargez PHP sur https://windows.php.net/download/" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entree pour quitter"
    exit 1
}

# ── Vérification Composer ────────────────────────────────────────────────────
Write-Host "[2/5] Verification Composer..." -ForegroundColor Cyan -NoNewline
$composerCheck = Get-Command composer -ErrorAction SilentlyContinue
if ($composerCheck) {
    Write-Host " OK" -ForegroundColor Green
} else {
    Write-Host " ERREUR" -ForegroundColor Red
    Write-Host "      Composer n'est pas installe." -ForegroundColor Red
    Write-Host "      Telechargez-le sur https://getcomposer.org/" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entree pour quitter"
    exit 1
}

# ── Vérification Symfony CLI ─────────────────────────────────────────────────
Write-Host "[3/5] Verification Symfony CLI..." -ForegroundColor Cyan -NoNewline
try {
    $symfonyVersion = (symfony version 2>$null | Select-String "Symfony CLI" | ForEach-Object { $_.ToString() })
    Write-Host " OK" -ForegroundColor Green
} catch {
    Write-Host " ERREUR" -ForegroundColor Red
    Write-Host "      Symfony CLI n'est pas installe." -ForegroundColor Red
    Write-Host "      Telechargez-le sur https://symfony.com/download" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entree pour quitter"
    exit 1
}

# ── Installation des dépendances si nécessaire ───────────────────────────────
Write-Host "[4/5] Verification des dependances Composer..." -ForegroundColor Cyan -NoNewline
if (-not (Test-Path "$projectPath\vendor")) {
    Write-Host "" 
    Write-Host "      Installation des dependances (premiere fois)..." -ForegroundColor Yellow
    composer install --no-interaction --quiet
    Write-Host "      Dependances installees." -ForegroundColor Green
} else {
    Write-Host " OK (vendor/ present)" -ForegroundColor Green
}

# ── Vérification du fichier .env ─────────────────────────────────────────────
if (-not (Test-Path "$projectPath\.env.local") -and -not (Test-Path "$projectPath\.env")) {
    Write-Host ""
    Write-Host "  ATTENTION : Aucun fichier .env trouve." -ForegroundColor Yellow
    Write-Host "  Copiez .env en .env.local et configurez DATABASE_URL." -ForegroundColor Yellow
    Write-Host ""
}

# ── Vérification et démarrage MySQL ──────────────────────────────────────────
Write-Host "[5/6] Verification MySQL..." -ForegroundColor Cyan -NoNewline

$mysqlRunning = $false

# 1. Vérifier via Get-Service (plus fiable que TCP — fonctionne même si MySQL écoute sur IPv6)
$mysqlService = Get-Service -Name "MySQL*" -ErrorAction SilentlyContinue |
    Where-Object { $_.Status -eq "Running" } |
    Select-Object -First 1

if ($mysqlService) {
    $mysqlRunning = $true
    Write-Host " OK ($($mysqlService.Name))" -ForegroundColor Green
} else {
    Write-Host " Arrete, tentative de demarrage..." -ForegroundColor Yellow

    $services = @("MySQL80", "MySQL", "mysql80", "mysql", "xampp_mysql")
    $started = $false

    foreach ($svc in $services) {
        try {
            Start-Service -Name $svc -ErrorAction Stop
            Start-Sleep -Seconds 3
            $started = $true
            Write-Host "  MySQL demarre ($svc)." -ForegroundColor Green
            break
        } catch {}
    }

    if (-not $started) {
        Write-Host ""
        Write-Host "  ERREUR : Impossible de demarrer MySQL automatiquement." -ForegroundColor Red
        Write-Host "  Ouvrez le panneau XAMPP et demarrez MySQL manuellement." -ForegroundColor Yellow
        Read-Host "  Appuyez sur Entree une fois MySQL demarre"
    }
}

# ── Lancement du serveur ─────────────────────────────────────────────────────
Write-Host "[6/6] Demarrage du serveur Symfony..." -ForegroundColor Cyan
Write-Host ""

# Récupérer l'IP locale du PC sur le réseau WiFi
$localIP = (Get-NetIPAddress -AddressFamily IPv4 |
    Where-Object { $_.InterfaceAlias -match "Wi-Fi|WiFi|Ethernet" -and $_.IPAddress -notmatch "^169" } |
    Select-Object -First 1).IPAddress

# Lancer le serveur accessible sur tout le réseau
$serverJob = Start-Job -ScriptBlock {
    Set-Location $using:projectPath
    symfony server:start --no-tls --listen-ip=0.0.0.0 2>&1
}

Write-Host "  Attente du demarrage..." -ForegroundColor DarkGray
Start-Sleep -Seconds 3

# Détecter le port réel
$statusOutput = symfony server:status --no-ansi 2>$null
$port = "8000"
if ($statusOutput -and ($statusOutput | Select-String -Pattern "http://127\.0\.0\.1:(\d+)")) {
    $port = ($statusOutput | Select-String -Pattern "http://127\.0\.0\.1:(\d+)").Matches[0].Groups[1].Value
}

$localUrl   = "http://127.0.0.1:$port"
$networkUrl = if ($localIP) { "http://${localIP}:$port" } else { "Non detectee" }

Write-Host "  Acces local   : $localUrl" -ForegroundColor Green
Write-Host "  Acces reseau  : $networkUrl" -ForegroundColor Yellow
Write-Host "  (entrez l'adresse reseau sur votre telephone)" -ForegroundColor DarkGray
Write-Host ""
Write-Host "  Appuyez sur Ctrl+C pour arreter." -ForegroundColor DarkGray
Write-Host ""

Start-Process $localUrl

Receive-Job -Job $serverJob -Wait -AutoRemoveJob