<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Marketspace API

## Description

Welcome to the Marketspace API! This project provides a Laravel-based backend for the [Marketspace App](https://github.com/SavioNicodemos/marketspace) that is a marketplace application, similar to OLX, but streamlined for efficiency. It focuses on product listings and user management, providing you with all the necessary operations to build a front-end to your liking.

## Features

1. **User Management:** Register, login, update and manage user profiles.
2. **Product Listings:** Create, read, update, and delete product listings.
3. **Search and Filter:** Find specific products and filter results based on various parameters.

Please note that this API does not handle transactions; it is designed to be a simple and flexible backend for a marketplace platform.

## Getting Started

### Prerequisites

Ensure you have the following installed on your local development machine:

- PHP >= 8.1
- Composer

### Installation

PS: We are going to use [Docker](https://www.docker.com/products/docker-desktop/) to install the project, but you can feel free to installed locally. And sadly to the Laravel Sail work we need to install the packages first with php and composer, but it's not required to run the app itself.

1. Clone the repository to your local machine:
```bash
git clone https://github.com/SavioNicodemos/marketspace-api-laravel
```

2. Install dependencies via composer:
```bash
cd marketspace-api-laravel
composer install
```
3. Configure your `.env` file for the database connection. Use the `.env.example` as a template.

4. Start the sail to run the application:  
*All `sail` command should run on the main dir of the project.  
*Additionally you can create an [alias](https://laravel.com/docs/10.x/sail#configuring-a-shell-alias) for have a shorter sail command
```bash
./vendor/bin/sail up
```
5. Run the database migrations:
```bash
./vendor/bin/sail artisan migrate --seed
```
Now you should now be able to access the API via http://localhost:8000/, but all the API routes are served in  http://localhost:8000/api/v1

## Documentation
Doccumentation in progress, but will be develop with Swagger. But for now you can have all the routes on the file of `Insomnia_marketspace_laravel` in the main dir of the app. Just drag the file inside of the Insomnia desktop app or import it.

## Testing
We use PHPUnit for testing. To run the tests:
```bash
./vendor/bin/sail artisan test
```
## Contributions
We welcome contributions from everyone. Please see the [CONTRIBUTING.md](https://github.com/SavioNicodemos/marketspace/blob/main/CONTRIBUTING.md) file for more information on how to contribute.

## License
This project is open-sourced software licensed under the MIT license.

## Contact
If you have any questions, feel free to reach out to us.

## Acknowledgments
- Laravel
- Sanctum
- PHPUnit
