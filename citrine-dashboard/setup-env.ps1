# CitrineOS Dashboard Setup Script
# This script sets up PHP and Composer for the current PowerShell session

Write-Host "=== CitrineOS Dashboard Setup ===" -ForegroundColor Cyan
Write-Host ""

# Add PHP to PATH (installed by winget)
$phpPath = "C:\Program Files\PHP\v8.2"
if (Test-Path $phpPath) {
    $env:Path = "$phpPath;$env:Path"
    Write-Host "[✓] PHP 8.2 added to PATH" -ForegroundColor Green
} else {
    Write-Host "[!] PHP not found at $phpPath" -ForegroundColor Yellow
    Write-Host "    Checking alternative locations..." -ForegroundColor Yellow
    
    # Check user profile
    $phpPathUser = "$env:LOCALAPPDATA\Programs\PHP\v8.2"
    if (Test-Path $phpPathUser) {
        $env:Path = "$phpPathUser;$env:Path"
        Write-Host "[✓] PHP 8.2 added to PATH from user profile" -ForegroundColor Green
    }
}

# Add Composer to PATH
$composerPath = "C:\Program Files (x86)\ComposerSetup\bin"
if (Test-Path $composerPath) {
    $env:Path = "$composerPath;$env:Path"
    Write-Host "[✓] Composer added to PATH" -ForegroundColor Green
} else {
    Write-Host "[!] Composer not found at $composerPath" -ForegroundColor Yellow
}

# Alternative Composer location
$composerPathAlt = "C:\ProgramData\ComposerSetup\bin"
if (Test-Path $composerPathAlt) {
    $env:Path = "$composerPathAlt;$env:Path"
    Write-Host "[✓] Composer added to PATH (alternative location)" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== Verifying Installations ===" -ForegroundColor Cyan
Write-Host ""

# Verify PHP
try {
    $phpVersion = & php --version 2>&1 | Select-Object -First 1
    Write-Host "[✓] PHP: $phpVersion" -ForegroundColor Green
} catch {
    Write-Host "[✗] PHP not found. Please restart your terminal." -ForegroundColor Red
}

# Verify Composer
try {
    $composerVersion = & composer --version 2>&1 | Select-Object -First 1
    Write-Host "[✓] Composer: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "[✗] Composer not found. Please restart your terminal." -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Next Steps ===" -ForegroundColor Cyan
Write-Host "1. Run: composer install" -ForegroundColor White
Write-Host "2. Run: npm install" -ForegroundColor White
Write-Host "3. Run: php artisan migrate" -ForegroundColor White
Write-Host "4. Run: php artisan serve (in one terminal)" -ForegroundColor White
Write-Host "5. Run: npm run dev (in another terminal)" -ForegroundColor White
Write-Host ""
