# Westay - Stay happy together

## Getting Started

### Installation
1. clone this repo `git clone https://github.com/daolvcntt/ws_api.git`
2. Copy .env file `$ cp .env.example .env`
3. Add this value `HppeDRXLesacFwztbgHrdpQGbBBDrMXz` to **APP_KEY** in .env file
4. Run this bash commands following
```bash
    $ composer install
    $ php artisan migrate
    $ php artisan db:seed
    $ php artisan passport:install --force
```

5. After run passport in command, copy the client id and key to .env file. It will look like this:
```.env
    CLIENT_ID       = **YOUR_CLIENT_ID**    (Eg:2)
    CLIENT_SECRET   = **YOUR_KEY**          (Eg:j5McHi18lYEUphLJmSoDyDiWMHIxvxQZKf3OyBtm)
```

### Setting up Insomnia

In your .env file, change the **APP_URL** value with your local web address
```.env
    APP_URL =    **YOUR_WEB_HERE**   (Eg:http://localhost:82/ws_api/public)
```
Go into Insomnia and change your local environment
```
    "api_url": "**YOUR_WEB_HERE**"  (Eg:http://localhost:82/ws_api/public/api/)
```
Login with your account in Insomnia, then copy the **access_token** and paste to local environment like this format
```
    "token": {
        .....
        "value": "Bearer **access_token**"
    }
```
