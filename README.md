# Blue Energy API

## How To Setup

### Dnsmasq

- Install and setup [Dnsmasq](https://passingcuriosity.com/2013/dnsmasq-dev-osx/)

**Docker Setup**

- Once that you have download the repository, open that folder via terminal

- Execute that command in order to setup the docker-compose override (that can differ from user ot user)

    ```cp docker-compose.override.yml.dist docker-compose.override.yml```

- Now it's time to setup the env file

    ```cp .env.dist .env```

- Launch the Docker compose

    ```docker-compose up -d```

- [Download certs](https://drive.google.com/drive/folders/1V8lEB9koqBFZxS6THUAB2G5T_zQxSriI?usp=sharing) and move in that folder:

    ```config/docker/nginx-proxy/certs```

**Composer**

- Once that Docker Compose is Up, go in Web container:

    ```docker-compose exec web bash```

- And install the composer!

    ```composer install```

**Setup the Database**

- Generate a new DB and update the schema with pre-compiled entities

    ```./bin/console doctrine:database:create```

    ```./bin/console doctrine:schema:update --force```

**Reload Docker**

- You need to restart Docker in order to make it work 

    ```docker-compose down && docker-compose up -d```
    
## How It Works

**GET**

- Get last meter-read of a customer/mpxn with `GET /meter-api` passing `custumerID` and `mpxn` as query parameters. 

    **Returns**
    - If query parameters missing `custumerID/mpxn`, you will receive an error 422
    - If the user doesn't exist you will receive a custom error message
    - If the user doesn't have any Meter (MPXN) associated you will receive a custom error message
    - If the user exist and have a Meter associated, you will receive all Meter-Reads for that customer

**POST**

- Post a new meter-read with `POST /meter-api` with these query parameters:

    *   `custumerId` (string)
    *   `serialNumber` (string)
    *   `mpxn` (string 6-11 or 21 digits)
    *   `registerId` (string)
    *   `type`  (string)
    *   `value` (int)
    
    **Returns**
    
    - All parameters will be validated by `validatePostParameters()`. If query parameters missing, you will receive an error 422
    - `Mpxn` will be used to determine if the MeterRead is GAS or ELECTRICITY Type based on the mpxn lenght
        - If the MPXN don't respect the validation reqs, you will receive a custom error message
    - If all variables are validated:
        - Get the customer (if exist in DB) or make a new customer
        - Get the meter associated to customer (if exist in DB) or make a new meter associated to the customer
        - Register a new MeterRead for the current meter associated to the customer
        - Return the MeterRead just recorded

**Entities**

- (A) Customer
- (B) Meters associated to specific customer (B->A)
- (C) MetersReads associated to specific Meter (C->B)

**Interesting info**

- Customer entity can be easily improved with further personal information 
- Customer Entity will be never duplicated
- Customer can be associated to more than a Meter (MPXN)
- MPXN (building Meter) can be associated to more than a Customer (ideal for new building owners). But not at the same time

### How To Test API

- Run this project by ```docker-compose up -d```

    - Open the web container (after docker-compose up -d) ``` docker-compose exec web bash ``` and execute:
      ```./vendor/bin/phpunit tests/Controller/ApiControllerTest.php```

    - Or you can customize GET/POST requests with a pre-built [Postman Collection](https://www.getpostman.com/collections/c0bda88aee80fae148c7). 


###What I've Done?!

- Setup a Symfony project with a skeleton

- Setup doctrine with MySQL

- Setup entities with getter and setters

- Setup FOSRestController for API

- Setup API

- Done some manual tests

- Setup Tests

- Code cleanup

- Write a complete Readme