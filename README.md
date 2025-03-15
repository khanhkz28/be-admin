# Project Setup and Running Guide

## Prerequisites

Before you start, ensure you have the following installed on your machine:

- Docker
- Docker Compose
- PHP (version 8.0)
- Composer
- MySQL (version 8.0)

## Project Structure

### 1. Set Up Environment Variables

Copy the `.env.example` file to `.env` and adjust the environment variables as needed:

```sh
cp .env.example .env
```

### 2. Build and Run Docker Containers

Use Docker Compose to build and run the containers:

```sh
docker-compose up --build -d
```

This will build the Docker images and start the services defined in the `docker-compose.yml` file.

### 3. Install PHP Dependencies

Once the containers are up and running, install the PHP dependencies using Composer:

```sh
docker-compose exec app composer install
```

### 4. Run Migrations

Run the database migrations to set up the database schema:

```sh
docker-compose exec app php artisan migrate
```

### 5. Seed the Database (Optional)

If you have seeders defined, you can seed the database:

```sh
docker-compose exec app php artisan db:seed
```

### 8. Access the Application

The application should now be running and accessible at [http://localhost:8081](http://localhost:8081).

## Additional Commands

### Artisan Commands

You can run any Artisan command using Docker Compose:

```sh
docker-compose exec app php artisan <command>
```

