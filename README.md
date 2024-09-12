# Project Name

Brief description of the project.

## Prerequisites

- [DDEV](https://ddev.readthedocs.io/en/stable/) installed on your system
- [Docker](https://www.docker.com/get-started) installed and running

## Local Environment Setup

Follow these steps to set up the project in your local environment:

1. Clone the repository: https://github.com/tantrumTP/bussiness-connect.git
2. Start DDEV: ddev start
3. Install PHP dependencies: ddev composer install
4. Generate Laravel application key: ddev exec php artisan key:generate
5. Run database migrations: ddev exec php artisan migrate
6. Create the storage symbolic link: ddev exec php artisan storage:link
7. (Optional) If you want to populate the database with test data: ddev exec php artisan db:seed --class=DatabaseSeeder