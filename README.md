# Elastic APM Subscriber for Symfony

This library supports Span traces of Symfony HttpKernel requests.
This library is based on [Elastic APM for Symfony HttpKernel](https://github.com/PcComponentes/apm-symfony-http-kernel).

## Installation

Install via [composer](https://getcomposer.org/)

```shell script
composer require jamarcer/symfony-httpkernel-apm
```

## Usage

It is necessary to have a previously created [ElasticApmTracer](https://github.com/zoilomora/elastic-apm-agent-php) instance.

```shell script
apm.tracer:
    class: ZoiloMora\ElasticAPM\ElasticApmTracer
    factory: ['App\Service\ApmService', 'instantiate']
    arguments: ['apm-desa','http://localhost:7200','desa']
```

### Service Container

```shell script
Jamarcer\APM\Symfony\Component\HttpKernel\EventSubscriber:
    class: Jamarcer\APM\Symfony\Component\HttpKernel\EventSubscriber
    tags: [ { name: kernel.event_subscriber } ]
    autoconfigure: true
    arguments:
        $router: '@router'
        $elasticApmTracer: '@apm.tracer'
```

## Development

Prepare the development environment. 

```shell script
make build
```

```shell script
make composer-install
```

Or you can access directly to bash ...

```shell script
make start
```

... and test the library

```shell script
/var/app/vendor/bin/phpunit  --configuration /var/app/phpunit.xml.dist 
```

## License

Licensed under the [MIT license](http://opensource.org/licenses/MIT).

Read [LICENSE](LICENSE) for more information