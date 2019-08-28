# Test API Backend

## Steps to run test

    1. Clonar proyecto **git clone https://github.com/yerconunez/TestBackend.git** into cd  ApiBackend.
    2. Run the command:  **docker-compose up -d**.
    3. Run the command: **docker exec -it apibackend_app_1 /bin/bash** to enter to container docker.
    4. After that run: **cd..** to change directory to /var/www from /var/www/html.
    5. Run:**php artisan key:generate**.
    6. Run:**php artisan cache:clear**.
    7. Run:**php artisan jwt:secret**.
    7. Run: **exit to log out from container's bash**.
    8. Once you finish the test run: **docker-compose down** to shut down the services and remove the images.

## Routes API Backend
The information of API is in file api.raml. The route base of API: localhost:8080/api/

| Route  | Method  |  JWT Token  |  Parameters                       |
|--------|---------|-------------|-----------------------------------|
| new    |  POST   |     ---     |   name, username, email, password | 
| login  |  POST   |     ---     |   email, password                 |
| me     |  GET    |    Token    |   Authorization: token            |
| user   |  POST   |    Token    |   Authorization: token            |
| user   |  DELETE |    Token    |   Authorization: token            |
| logout |  GET    |    Token    |   Authorization: token            |
    
 
    

