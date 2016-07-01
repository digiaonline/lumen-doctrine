# Lumen Doctrine ORM

[![Code Climate](https://codeclimate.com/github/nordsoftware/lumen-doctrine/badges/gpa.svg)](https://codeclimate.com/github/nordsoftware/lumen-doctrine)
[![StyleCI](https://styleci.io/repos/35571355/shield?style=flat)](https://styleci.io/repos/35571355)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nordsoftware/lumen-doctrine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nordsoftware/lumen-doctrine/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/nordsoftware/lumen-doctrine/version)](https://packagist.org/packages/nordsoftware/lumen-doctrine)
[![Total Downloads](https://poser.pugx.org/nordsoftware/lumen-doctrine/downloads)](https://packagist.org/packages/nordsoftware/lumen-doctrine)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Gitter](https://img.shields.io/gitter/room/norsoftware/open-source.svg?maxAge=2592000)](https://gitter.im/nordsoftware/open-source)

[Doctrine](http://www.doctrine-project.org/projects/orm.html) module for the [Lumen PHP framework](http://lumen.laravel.com/).

## Requirements

- PHP 5.5 or newer
- [Composer](http://getcomposer.org)

## Usage

### Installation

Run the following command to install the package through Composer:

```sh
composer require nordsoftware/lumen-doctrine
```

### Bootstrapping

Add the following line to ```bootstrap/app.php```:

```php
$app->register('Nord\Lumen\Doctrine\ORM\DoctrineServiceProvider');
```

You can now use the ```EntityManager``` facade or inject the ```EntityManagerInterface``` where needed.

### Configure

Copy ```config/doctrine.php``` into ```config``` and modify according to your needs.

The available configurations are:

- **mapping** - Mapping driver to use (xml, yaml or annotations), defaults to xml
- **paths** - Paths to entity mappings, defaults to an empty array
- **types** - Custom Doctrine types to register, defaults to an empty array
- **proxy** - Proxy configuration
- **repository** - Repository class to use
- **logger** - Logger class to use

### Run Artisan

Run ```php artisan``` and you should see the new commands in the doctrine:* namespace section.

## Contributing

Please read the [guidelines](.github/CONTRIBUTING.md).

## License

See [LICENSE](LICENSE).
