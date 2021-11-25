# Laravel geonames.

![Packagist License](https://img.shields.io/packagist/l/yaroslawww/laravel-geonames?color=%234dc71f)
[![Build Status](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/badges/build.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/yaroslawww/laravel-geonames/?branch=master)

Import and manipulate geonames data in your project.

## Installation

Install the package via composer:

```bash
composer require yaroslawww/laravel-geonames
# or
composer require yaroslawww/laravel-geonames --dev
```

Optionally you can publish the config file with:

```bash
php artisan vendor:publish --provider="LaraGeoData\ServiceProvider" --tag="config"
```

Allow import local infile:

```injectablephp
[
    'mysql_geo' => [
        // ....
        'options'    => [PDO::MYSQL_ATTR_LOCAL_INFILE=>true],
     ],
]
```

## Usage

Download dump files:

```shell
php artisan geonames:download GB.zip FR.zip --postal=GB.zip --postal=GB_full.csv.zip --postal=FR.zip --extract --force
```

Clear all downloaded files:

```shell
php artisan geonames:download:truncate
```

Make migrations

```shell
php artisan geonames:make:migration geonames
# or
php artisan geonames:make:migration geonames --suffix=gb

php artisan geonames:make:migration postalcodes --suffix=gb
```

Import data

```shell
php artisan geonames:import:file-to-db geonames
# or
php artisan geonames:import:file-to-db geonames --suffix=gb
# or
php artisan geonames:import:file-to-db geonames path/to/my/file.csv --suffix=gb

php artisan geonames:import:file-to-db postalcodes --suffix=gb
php artisan geonames:import:file-to-db postalcodes postal_codes/GB_full.txt --suffix=gb
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 
