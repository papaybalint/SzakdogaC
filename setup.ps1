# .env fájl létrehozása .env.example másolásával
Write-Host "Másolom a .env.example fájlt .env-re..."
Copy-Item -Path ".env.example" -Destination ".env"

# Composer függőségek telepítése
Write-Host "Frissítem a Composer-t..."
composer u

# Npm függőségek telepítése
Write-Host "Telepítem az npm függőségeket..."
npm install

# Laravel kulcs generálása
Write-Host "Generálom a Laravel alkalmazás kulcsát..."
php artisan key:generate

# Táblák tölrése, migráció futtatása és adatok betöltése
Write-Host "Törlöm a táblákat, futtatom a migrációkat és betöltöm az adatokat..."
php artisan migrate:fresh --seed

# Szimbolikus link létrehozása
Write-Host "Szimbolikus link létrehozása..."
php artisan storage:link

# Laravel és Npm fejlesztői szerver indítása
Write-Host "Laravel és Npm fejlesztői szerver indítása..."
Start-Process npm -ArgumentList "run dev"
php artisan serve
Write-Host "Minden lépés sikeresen befejeződött."
