## Installation

git clone https://github.com/username/hotel-management.git
cd hotel-management
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
