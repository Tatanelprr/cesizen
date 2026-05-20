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

# Tester la connexion MySQL
try {
    $testConn = New-Object System.Net.Sockets.TcpClient
    $testConn.Connect("127.0.0.1", 3306)
    $testConn.Close()
    $mysqlRunning = $true
    Write-Host " OK" -ForegroundColor Green
} catch {
    Write-Host " Arrete, tentative de demarrage..." -ForegroundColor Yellow
}

if (-not $mysqlRunning) {
    # Essayer de démarrer via XAMPP
    $xamppMysql = "C:\xampp\mysql\bin\mysqld.exe"
    $services = @("mysql", "mysql80", "MySQL80", "MySQL", "xampp_mysql")
    
    $started = $false
    foreach ($svc in $services) {
        try {
            Start-Service -Name $svc -ErrorAction SilentlyContinue
            Start-Sleep -Seconds 3
            $testConn = New-Object System.Net.Sockets.TcpClient
            $testConn.Connect("127.0.0.1", 3306)
            $testConn.Close()
            $started = $true
            Write-Host "  MySQL demarre." -ForegroundColor Green
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

# Lancer le serveur en arrière-plan pour capturer le port
$serverJob = Start-Job -ScriptBlock {
    Set-Location $using:projectPath
    symfony server:start --no-tls 2>&1
}

# Attendre que le serveur démarre et détecter le port
Write-Host "  Attente du demarrage..." -ForegroundColor DarkGray
Start-Sleep -Seconds 3

# Récupérer l'URL réelle depuis symfony server:status
$statusOutput = symfony server:status --no-ansi 2>$null
$serverUrl = "http://127.0.0.1:8000"

if ($statusOutput -and ($statusOutput | Select-String -Pattern "http://127\.0\.0\.1:(\d+)")) {
    $match = ($statusOutput | Select-String -Pattern "http://127\.0\.0\.1:(\d+)").Matches[0]
    $serverUrl = $match.Value
}

Write-Host "  Application disponible sur : $serverUrl" -ForegroundColor Green
Write-Host "  Appuyez sur Ctrl+C pour arreter le serveur." -ForegroundColor DarkGray
Write-Host ""

# Ouvrir le navigateur sur le bon port
Start-Process $serverUrl

# Afficher les logs du serveur en temps réel
Receive-Job -Job $serverJob -Wait -AutoRemoveJob