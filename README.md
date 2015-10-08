# Lumen Doctrine ORM

[Doctrine](http://www.doctrine-project.org/projects/orm.html) module for the [Lumen PHP framework](http://lumen.laravel.com/).

## Requirements

- PHP >= 5.5

## Usage

### Install through Composer

Run the following command to install the package:

```sh
composer require nordsoftware/lumen-doctrine
```

### Register the Service Provider

Add the following line to ```bootstrap/app.php```:

```php
$app->register('Nord\Lumen\Doctrine\ORM\DoctrineServiceProvider');
```

You can now use the ```EntityManager``` facade or inject the ```EntityManagerInterface``` where needed.

### Configure

Copy ```config/doctrine.php``` into ```config``` and modify according to your needs.

The available configurations are:

- **mapping** - Mapping driver to use (xml, yaml or annotations), defaults to xml
- **mappingextension** - Configures the file extension for mapping files
- **paths** - Paths to entity mappings, defaults to an empty array
- **yamlpaths** - Paths to entity mappings including namespace identifiers
- **types** - Custom Doctrine types to register, defaults to an empty array
- **proxy** - Proxy configuration
- **repository** - Repository class to use
- **logger** - Logger class to use

### Run Artisan

Run ```php artisan``` and you should see the new commands in the doctrine:* namespace section.

## Contributing

Please note the following guidelines before submitting pull requests:

- Use the [PSR-2 coding style](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

## License

See [LICENSE](LICENSE).
