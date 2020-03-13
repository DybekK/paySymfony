# budget-app-symfony


server-side of [budget-app](https://github.com/DybekK/budget-app)

## Installation

Read [Symfony Docs](https://reactnative.dev/docs/getting-started) to install dependencies. 
PHP 7.4 is required.

## Usage
Follow these steps to run server:

###### install packages:

`
composer install 
`

###### configure [.env](https://symfony.com/doc/current/doctrine.html) file and run:

`
bin/console doctrine:database:create
`

###### create and update schema:

`
bin/console doctrine:schema:create
`

`
bin/console doctrine:schema:update --force
`

###### Run server:

`
php -S your-local-ip:port -t public
`
###### for example

`
php -S 192.168.1.187:8000 -t public
`

## License
[MIT](https://choosealicense.com/licenses/mit/)
