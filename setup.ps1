# .env fájl létrehozása .env.example másolásával
Write-Host "Másolom a .env.example fájlt .env-re..."
Copy-Item -Path ".env.example" -Destination ".env"

# Composer függőségek telepítése
Write-Host "Függőségek telepítése composer-rel..."
composer install --no-interaction --prefer-dist

# Npm függőségek telepítése
Write-Host "Függőségek telepítése npm-mel..."
npm install

# Laravel kulcs generálása
Write-Host "Generálom a Laravel alkalmazás kulcsát..."
php artisan key:generate

# Migrációk futtatása
Write-Host "Futtatom a migrációkat..."
php artisan migrate

# Seed adatbázis
Write-Host "Seed-elem az adatbázist..."
php artisan db:seed

# Szimbolikus link létrehozása
Write-Host "Szimbolikus link létrehozása..."
php artisan storage:link

# Laravel szerver indítása új VS Code terminálban
Write-Host "Laravel szerver indítása..."
Start-Process "powershell.exe" -ArgumentList "php artisan serve"

# Npm fejlesztői szerver indítása új VS Code terminálban
Write-Host "Npm fejlesztői szerver indítása..."
Start-Process "powershell.exe" -ArgumentList "npm run dev"

Write-Host "Minden lépés sikeresen befejeződött."
