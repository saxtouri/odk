We suggest deploying with docker. If this option is not available on your system, we suggest XAMPP or a similar Apache2/MySQL/PHP stack.

Deploy with docker
------------------
1. Make sure docker compose is set up on your system: https://docs.docker.com/compose/install/
2. From the root directory of the current software, run:
    docker-compose up

The application must be accessible on "http://localhost".
To stop the application:
    docker-compose down

Docker troubleshooting
----------------------
Sometimes there are other applications using the port 80, 443 and/or 3306. If that's the case, stop the application and edit "docker-compose.yml". Change the ports accordingly.

For instance, to instruct the web server to run http on port "8080" change line 8 to
    - 80:8080

Also, if you need to modify the database connection settings (e.g., different db user name, different password, different database name), change the correspondig fields in both "docker-compose.yml" and "www/config.php".

For instance, to name your database "my_odk"
- change "docker-compose.yml" line 23 to:
    MYSQL_DATABASE: my_odk
- change "www/config.php" line 13 to:
    define('DB_DATABASE', 'my_odk');

Generic Installation instructions
---------------------------------
1. Setup a www server with php enabled. More information here: http://php.net/manual/en/install.php
2. Create a mysql or mariadb database with "utf8_unicode_ci" collation. Also, set up a user with full access to the database and note down their password.
3. Copy the content of folder "www" to wherever your webserver serves
content.
4. Edit "config.php" to setup the database connection. The following fields are mandatory:
// DB settings
define('DB_HOSTNAME', 'the host of your database, e.g. localhost');
define('DB_USERNAME', 'db user with access to your database');
define('DB_PASSWORD', 'db user password or empty (no blank spaces) if passwordless');
define('DB_DATABASE', 'the name of the database');

