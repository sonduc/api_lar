# Westay - Stay happy together

## Installation

1. clone this repo `git clone https://github.com/daolvcntt/ws_api.git`
2. Copy .env file `$ cp .env.example .env`
3. Add this value `HppeDRXLesacFwztbgHrdpQGbBBDrMXz` to APP_KEY in .env file
4. Run this bash commands following
```bash
    $ composer install
    $ php artisan migrate
    $ php artisan db:seed
    $ php artisan passport:install --force
```
