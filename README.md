# PHP OPDS

## NOT READY FOR PRODUCTION

[![php][php-version-src]][php-version-href]
[![version][version-src]][version-href]
[![downloads][downloads-src]][downloads-href]
[![license][license-src]][license-href]

[![tests][tests-src]][tests-href]
[![codecov][codecov-src]][codecov-href]

PHP package to create [OPDS feed](https://opds.io/) (Open Publication Distribution System) for eBooks.

## Requirements

-   PHP >= 8.1

## Installation

You can install the package via composer:

```bash
composer require kiwilan/php-opds
```

## Usage

```php
$skeleton = new Kiwilan\Opds();
echo $skeleton->echoPhrase('Hello, Kiwilan!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

-   [Ewilan Rivière](https://github.com/ewilan-riviere)
-   [spatie/array-to-xml](https://github.com/spatie/array-to-xml)
-   [spatie/package-skeleton-php](https://github.com/spatie/package-skeleton-php)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[version-src]: https://img.shields.io/packagist/v/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[version-href]: https://packagist.org/packages/kiwilan/steward-laravel
[php-version-src]: https://img.shields.io/static/v1?style=flat-square&label=PHP&message=v8.1&color=777BB4&logo=php&logoColor=ffffff&labelColor=18181b
[php-version-href]: https://www.php.net/
[downloads-src]: https://img.shields.io/packagist/dt/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[downloads-href]: https://packagist.org/packages/kiwilan/php-opds
[license-src]: https://img.shields.io/github/license/kiwilan/php-opds.svg?style=flat-square&colorA=18181B&colorB=777BB4
[license-href]: https://github.com/kiwilan/php-opds/blob/main/README.md
[tests-src]: https://img.shields.io/github/actions/workflow/status/kiwilan/php-opds/run-tests.yml?branch=main&label=tests&style=flat-square&colorA=18181B
[tests-href]: https://packagist.org/packages/kiwilan/php-opds
[codecov-src]: https://codecov.io/gh/kiwilan/php-opds/branch/main/graph/badge.svg?token=P9XIK2KV9G
[codecov-href]: https://codecov.io/gh/kiwilan/php-opds
