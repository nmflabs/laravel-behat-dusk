<p align="center" style="font-size: 25px">Laravel Behat Dusk</p>

<p align="center">
    <a href="https://travis-ci.org/nmfzone/laravel-behat-dusk"><img src="https://travis-ci.org/nmfzone/laravel-behat-dusk.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/nmfzone/laravel-behat-dusk"><img src="https://poser.pugx.org/nmfzone/laravel-behat-dusk/d/total.svg" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/nmfzone/laravel-behat-dusk"><img src="https://poser.pugx.org/nmfzone/laravel-behat-dusk/v/stable.svg" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/nmfzone/laravel-behat-dusk"><img src="https://poser.pugx.org/nmfzone/laravel-behat-dusk/license.svg" alt="License"></a>
</p>

## Introduction
Seamlessly integrate Behat with [Laravel Dusk](https://github.com/laravel/dusk).

### Requirements

    >= PHP 7.1

### Installation

```bash
$ composer require nmfzone/laravel-behat-dusk
```

If you want to change the default config, you must publish the config file:

```bash
$ php artisan vendor:publish --provider="Nmflabs\LaravelBehatDusk\ServiceProvider"
```

### Installation in Lumen

```bash
$ composer require nmfzone/laravel-behat-dusk
```

Next up, the service provider must be registered:

```php
// bootstrap/app.php

$app->register(Nmflabs\LaravelBehatDusk\ServiceProvider::class);
```

## Usage

Todo

## Security

If you discover any security related issues, please email to 123.nabil.dev@gmail.com instead of using the issue tracker.

## Credits

- [Nabil M. Firdaus](https://twitter.com/nmfzone)
- [All contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
