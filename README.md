Istrucciones para crear una API autenticada
composer create-project --prefer-dist laravel/laravel project-auth-api "11.*"
php artisan install:api
php artisan config:publish cors
composer require laravel/breeze --dev -W
composer require laravel/sanctum  -W
composer require guzzlehttp/guzzle  -W //mailtrap
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
EN el modelo User, agreghar:
use Laravel\Sanctum\HasApiTokens;
En la definición de la clase, agregar:
HasApiTokens (al final de use HasFactory, Notifiable;)
Los controlñadores deben ser creados con la opción --api al final
