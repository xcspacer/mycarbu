# Install & Setup

## Step 1 - Configure
First, make a copy of the example environment configuration file by running the following command:
```bash
cp .env.example .env
```

## Step 2 - Environment Setup
Use the following command to set up your environment:
```bash
./vendor/bin/sail up
```
This command will start the necessary Docker containers and set up your environment for the project.

## Step 3 - Access the Bash Shell in Docker
```bash
docker exec CONTAINER_NAME_HERE COMMAND_HERE
```
This command allows you to interact with the container for any specific tasks you might need to perform.

## Step 4 - Configuration and Dependencies
Ensure that your application is properly configured and has all the required dependencies. Execute the following commands one by one:
```bash
chmod -R 775 storage && chmod -R 775 bootstrap && rm -rf public/storage && php artisan key:generate && php artisan storage:link && php artisan migrate --seed && php artisan queue:work && npm run dev
```