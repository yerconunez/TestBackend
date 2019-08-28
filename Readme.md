# Test API Backend

## Steps to run test

    1.  Run the command: git clone https://github.com/yerconunez/TestBackend.git 
    2.  Run the command: cd TestBackend/ApiBackend
    3.  Run the command: composer install
    4.  Run the command: sudo docker-compose up -d  
    5.  To enter to container docker run the command: sudo docker exec -it apibackend_app_1 /bin/bash 
    6.  To change directory to /var/www/ run the command: cd .. 
    7.  Run command: php artisan key:generate
    8.  Run command: php artisan cache:clear
    9.  Run command: php artisan jwt:secret
    10. To log out from container run command: exit 
    11. To end the test run: docker-compose down

## Routes API Backend
The specific information of API is in the file api.raml. The route base of API: localhost:8080/api/

| Route  | Method  |  JWT Token  |  Parameters                       |
|--------|---------|-------------|-----------------------------------|
| new    |  POST   |     ---     |   name, username, email, password | 
| login  |  POST   |     ---     |   email, password                 |
| me     |  GET    |    Token    |   Authorization: token            |
| user   |  POST   |    Token    |   Authorization: token  / name, username, email, password  |    
| user   |  DELETE |    Token    |   Authorization: token            |
| logout |  GET    |    Token    |   Authorization: token            |
    
 
    

