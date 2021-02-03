# Getting Started: Docker Runbook

In order to create a working environment quickly and effectively, by using Docker we can create a simple environment using `docker-compose` to run the latest version of Wordpress and a database along side it with a means to running the wp-reseller-store plugin in a volume.

### Create a new project

Within your development directory create a new folder the name is not important but for this example we will name it `wp-reseller-dev`.

In your console navigate to this directory and create a file called `docker-compose.yml`

Copy the below contents in to the yml file:
```yml
version: '3.3'
services:
  db:
    image: mysql:5.7
    volumes:
      - db_data:/var/lib/mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: somewordpress
      MYSQL_DATABASE: wordpress
      MYSQL_USER: wordpress
      MYSQL_PASSWORD: wordpress
  wordpress:
    depends_on:
      - db
    image: wordpress:latest
    ports:
      - "8000:80"
    restart: always
    environment:
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: wordpress
      WORDPRESS_DB_PASSWORD: wordpress
      WORDPRESS_DB_NAME: wordpress
    volumes:
      - "./wordpress:/var/www/html"
      - "./plugins:/var/www/html/wp-content/plugins"
volumes:
    db_data: {}
```

Feel free to change the exposed port of `localhost:8080`.

Once this file has been saved within the `wp-reseller-dev` directory please run:

```
docker compose up
```

This will go off to DockerHub and pull down the requested docker images Wordpress and SQL.

### Create Wordpress Admin

Visit `localhost:8080` to view your docker Wordpress installation and follow the steps on screen to create your admin account.

Once logged in please you should now half 2 volumes in your `wp-reseller-dev` directory, `/wordpress` & `/plugins`

In your console `cd` in to `/plugins` folder and clone this repository in to it.

Within the wordpress admin you should now see the reseller store plugin listed in plugin view.

Click activate to enable and then visit the reseller store plugin menu item in the sidebar and follow on screen instructions.
