# Elastic APM Subscriber for Symfony

This library supports Span traces of Symfony HttpKernel requests.
This library is based on [Elastic APM for Symfony HttpKernel](https://github.com/PcComponentes/apm-symfony-http-kernel).

PHP: 8.0
Symfony: 5.2

## Installation

## Usage

## Development

Prepare the development environment. 

```shell script
make build
```

```shell script
make composer-install
```

Or you can access directly to bash

```shell script
make start
```

And test the library

```shell script
/var/app/vendor/bin/phpunit  --configuration /var/app/phpunit.xml.dist 
```

## License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).

Read [LICENSE](LICENSE) for more information