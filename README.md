## Restaurant Search

### Figma Design
![alt text](https://github.com/natnapat/Restaurant_search/blob/main/pics/Figma_design.png)

### Web App
![alt text](https://github.com/natnapat/Restaurant_search/blob/main/pics/web_app.png)

### How to run
set up docker
```
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

install dependencies
```
npm install
npm run dev
```

sail up
```
vendor/bin/sail up
```

