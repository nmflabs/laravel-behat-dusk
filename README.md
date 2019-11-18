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

- PHP >= 7.1

### Installation

```bash
$ composer require nmfzone/laravel-behat-dusk
```

If you want to change the default config, you must publish the config file:

```bash
$ php artisan vendor:publish --provider="Nmflabs\LaravelBehatDusk\ServiceProvider"
```

## Usage

> I assume you're using this package for the first time you setup behat (so there is no behat.yml, etc).

1. Run this command `php artisan behat-dusk:install`
2. Duplicate your `.env` to `.env.behat` (optional). This will be the environment used by behat.
3. Adjust `behat.yml` as you wish
4. To run the test, run this command `php artisan behat` (it's just wrapper of the original behat command.
It's automatically run `php artisan serve` for you, and stop it when test is done 🔥)
5. Enjoy!

## Security

If you discover any security related issues, please email to 123.nabil.dev@gmail.com instead of using the issue tracker.

## Credits

- [Nabil M. Firdaus](https://twitter.com/nmfzone)
- [All contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
