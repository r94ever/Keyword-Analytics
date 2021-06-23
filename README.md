# Laravel Keyword Analytics

Analyze keywords in the content and make diagnostics to improve the seo score of the content.

## Features

- Check keyword length.
- Check keyword density.
- Check title length.
- Check meta description length.
- Check content length.
- Check keyword in URL.
- Check keyword in title.
- Check keyword in first paragraph of content.
- Check keyword in meta description.
- Check images in content.
- Check keyword in ALT attribute of image tags.
- Check links in content.
- Check keyword in TITLE attribute of links.
- Check heading available in content.
- Check keyword available in headings.

## Requirements

- PHP7.0+
- ext-json
- ext-intl

## Installation

You can install the package via composer:

```bash
composer require qmas/laravel-keyword-analytics
```

#### For Laravel

The Service Provider has automatically discovered. You don't need to do anything to register it.

If you need to change configurations of this package, run the following command:

```bash
php artisan vendor:publish --provider="Qmas\KeywordAnalytics\KeywordAnalyticsServiceProvider"
```
#### For Lumen

Open `bootstrap/app.php` then add the following code to **Register Service Providers** section:

```php
$app->register(QMAS\KeywordAnalytics\KeywordAnalyticsServiceProvider::class);
```

#### For Lumen

Copy `config.php` from `vendor/qmas/laravel-keyword-analytics/config` to `config` directory then rename to `keyword-analytics.php`

## Usage

```php
use Qmas\KeywordAnalytics\Facade as Analytic;

$results = Analytic::run($keyword, $title, $description, $html, $url)->getResults();
```

Or you can run instance from request

```php
use Qmas\KeywordAnalytics\Facade as Analytic;

$results = Analytic::fromRequest()->run()->getResults();
```

### Testing

```bash
composer tests
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email dev@qmas.vn instead of using the issue tracker.

## Credits

-   [QMAS](https://github.com/qmas)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
