# Laravel PHP Framework

This is developed by Laravel 5.3 Version

Installation

git clone https://github.com/aalviian/iLibrary.git projectname
cd projectname
composer install
php artisan key:generate
Create a database and inform .env
php artisan migrate --seed to create and populate tables
Inform config/mail.php for email sends
php artisan vendor:publish to publish filemanager
php artisan serve to start the app on http://localhost:8000/