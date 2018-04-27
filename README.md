# How to run?
1. Clone repository to your local machine.```git clone git@github.com:pontifex/todo.git```
2. Go to newly created directory.```cd todo```
3. Update env configuration.```cp symfony/.env.dist symfony/.env```
4. Build docker.```docker-compose build```
5. Run docker.```docker-compose up```
6. Set permissions for log and cache.```docker exec -i container_phpfpm chmod 777 var -Rf```
7. Install composer dependencies.```docker exec -i container_phpfpm composer install```
8. Create db.```docker exec -i container_phpfpm php bin/console doctrine:schema:create```
9. Load fixtures```docker exec -i container_phpfpm php bin/console doctrine:fixtures:load```

After following steps above, website should be available on http://localhost. To get 
access to databse use root:root credentials and command below to get IP: 
```
docker network inspect bridge | grep Gateway | grep -o -E '[0-9\.]+'
```
