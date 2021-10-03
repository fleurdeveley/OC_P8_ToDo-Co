# How to contribute to the project ?

## 1/ Installation of the ToDo & Co project

Open the console.

Clone the project :
``` 
  git clone https://github.com/fleurdeveley/OC_P8_ToDo-Co.git
```

Enter the project folder :
``` 
  cd OC_P8_ToDo-Co
``` 

Configure your environment variables :
* Docker containers (at the root of the project) : 
``` 
  cp .env.example .env
``` 
* database URL : 
``` 
  cp app/html/.env app/html/.env.local && app/html/.env app/html/.env.test
``` 

Create the Docker network :
``` 
  docker network create project8
``` 

Launch the containers :
``` 
  docker-composer up -d
``` 

Enter the PHP container to launch the database commands :
``` 
  docker exec -ti [php container name (container id)] bash
``` 

Install composer and dependencies :
``` 
  compose install
``` 

Install the database :
``` 
  php bin/console make:migration
``` 
``` 
  php bin/console doctrine:migrations:migrate
``` 

Install the fixtures (dummy data demo) :
``` 
  php bin/console doctrine:fixtures:load
``` 

Leave the container :
``` 
  exit
``` 

Create a branch for each feature with an explicit name :
``` 
  git checkout -b BranchName
``` 

## 2/ Contribute to the project

### a/ Develop new functionalities

In order to offer a comprehensive and easily scalable code, write your code respecting the standards :
* the classes and methods must be commented out,
* the code must have a good presentation and indentation,
* the code must meet PSR standards (PSR-1, PSR-2, PSR-4 and PSR-12).

After each new feature, perform unit and functional tests covering the methods, functions and functionalities of the application.

For each piece of code working and tested, commit to your branch :
* by adding : 
``` 
  git add .
``` 
* by committing : 
``` 
  git commit -m “quick description of your files”
``` 
* by pushing your work on your branch : 
``` 
  git push origin nameOfTheBranch
``` 

Make a Pull Request :
* on the project repository, click on New Pull Request,
* compare the database: merge <- compare: name of your branch,
* click on the arrow next to Create Pull Request
* click on Draft.

NB : the Draft allows you to lock the Pull Request until your work is finished.

### b/ Merge your modifications

Check your code with :
* compliance with standards (see a / Developing new functionalities),
* quality : submit your code to a quality controller using Codacy (or CodeClimate or SymfonyInsight),
* performance : submit your code to a performance checker using BlackFire,
* make corrections if necessary. 

Check that all the tests turn green in the php container :
``` 
  composer tests
``` 
This command launches the unit and functional tests to obtain a coverage report in HTML, located in app \ html \ coverage.

Your work on your feature is finished:
* remove the Draft,
* click on Merge pull request.
