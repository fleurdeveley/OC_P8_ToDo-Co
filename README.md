# OC_P8_ToDo-Co

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/4c042271eef54a0ea00b77be1288a036)](https://www.codacy.com/gh/fleurdeveley/OC_P8_ToDo-Co/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=fleurdeveley/OC_P8_ToDo-Co&amp;utm_campaign=Badge_Grade)

## Description of the project
* As a part of study project, improve an existing ToDo & Co application.

## Technologies
* PHP 7.4
* Symfony 5.3
* Composer 1.10.1
* Git

## PHP Dependencies
* [symfony/security-bundle](https://github.com/symfony/security-bundle)
* [phpunit/phpunit](https://github.com/sebastianbergmann/phpunit)
* [doctrine/doctrine-bundle](https://github.com/doctrine/DoctrineBundle)
* [liip/test-fixtures-bundle](https://github.com/liip/LiipTestFixturesBundle)

## Source
1. Clone the GitHub repository :
```
   git clone https://github.com/fleurdeveley/OC_P8_ToDo-Co.git
```

## Installation
2. Enter the project file :
```
  cd OC_P8_ToDo-Co
```

3. Configure your environment variables :
* Docker containers, at the root of the project :
```
  cp .env.example .env
```
* SMTP server and database :
```
  cp app/html/vendor/.env app/html/vendor/.env.local app/html/vendor/.env.test
```

4. Create the docker network
```
  docker network create project8
```

5. Launch the containers
```
  docker-composer up -d
```

6. Enter the PHP container to launch the commands for the database
```
  docker exec -ti [nom du container php] bash
```

7. Install php dependencies with composer
```
  composer install
```

8. Install the database
```
  php bin/console doctrine:migrations:migrate
```

9. Install the fixture (dummy data demo)
```
  php bin/console doctrine:fixtures:load
```

11. Leave the container
```
  exit
```

## Database
* Connection to PHPMyAdmin : http://localhost:8080
* Server : project8_mysql
* User : admin
* Password : password

## Access to ToDo & Co application
* http://localhost:8080/

## Credentials
* Login : username0
* Password : password

## Author
Fleur (https://github.com/fleurdeveley)
