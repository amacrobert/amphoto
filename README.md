amphoto
=======

Site generator for andrewmacrobert.com

### Running the site locally
```bash
docker-compose up --build -d
docker exec -it amphoto composer install
```

Then go to https://127.0.0.1:8080.

### Building and deploying the site

While the above Docker container is running, build the static site and deploy it with
```
bin/deploy
```
