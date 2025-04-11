# .env fájl létrehozása .env.example másolásával
Write-Host "Másolom a .env.example fájlt .env-re..."
Copy-Item -Path ".env.example" -Destination ".env"

# Composer függőségek telepítése
Write-Host "Függőségek telepítése composer-rel..."
composer u

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

# Laravel és Npm fejlesztői szerver indítása
Write-Host "Laravel és Npm fejlesztői szerver indítása..."
Start-Process npm -ArgumentList "run dev"
php artisan serve
Write-Host "Minden lépés sikeresen befejeződött."
