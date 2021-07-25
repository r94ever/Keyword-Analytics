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

#### Language Supported

- English
- Chinese
- Spanish
- French
- Indonesian
- Japanese
- Portuguese
- Russian
- Vietnamese

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

Copy `config.php` from `vendor/qmas/laravel-keyword-analytics/config` to `config` directory then rename to `keyword-analytics.php`

Open `bootstrap/app.php` then add the following code to **Create Application** section:

```php
$app->configure('keyword-analytics');
```

And the following code to **Register Service Providers** section:

```php
$app->register(QMAS\KeywordAnalytics\KeywordAnalyticsServiceProvider::class);
```

## Usage

#### For Laravel

```php
use Qmas\KeywordAnalytics\Facade as Analytic;

$results = Analytic::run($keyword, $title, $description, $html, $url)->getResults();

// Or
$results = app('keyword-analytics')->run($keyword, $title, $description, $html, $url)->getResults();

dd($results);
```

Or you can run instance from request (read config file to understand about this method)

```php
use Qmas\KeywordAnalytics\Facade as Analytic;

$results = Analytic::fromRequest()->run()->getResults();

// Or
$results = app('keyword-analytics')->fromRequest()->run()->getResults();

dd($results);
```

#### For Lumen

```php
use Qmas\KeywordAnalytics\Analysis;

$results = app(Analysis::class)->run($keyword, $title, $description, $html, $url)->getResults();

dd($results);
```

Or you can run instance from request

```php
use Qmas\KeywordAnalytics\Analysis;

$results = app(Analysis::class)->fromRequest()->run()->getResults();

dd($results);
````

### Testing

```bash
composer tests
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email vandt147@outlook.com instead of using the issue tracker.

## Credits

-   [r94ever](https://github.com/r94ever)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
