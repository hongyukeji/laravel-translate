<h1 align="center">Laravel语言翻译 - 翻译，从未如此简单</h1>

<p align="center">
<a href="https://packagist.org/packages/hongyukeji/laravel-translate"><img src="https://poser.pugx.org/hongyukeji/laravel-translate/v/stable" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/hongyukeji/laravel-translate"><img src="https://poser.pugx.org/hongyukeji/laravel-translate/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/hongyukeji/laravel-translate"><img src="https://poser.pugx.org/hongyukeji/laravel-translate/v/unstable" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/hongyukeji/laravel-translate"><img src="https://poser.pugx.org/hongyukeji/laravel-translate/license" alt="License"></a>
</p>

> 全网首款支持所有语言翻译插件，自由扩展，无缝对接。

> 采用鸿宇科技专利[《宏观设计模式》](docs/README.md)开发。

> 更多请点击 [https://www.hongyuvip.com](https://www.hongyuvip.com)

## 特点

- 支持全网络翻译服务商
- 支持自由扩展且易维护

## 支持

- [百度翻译](http://fanyi-api.baidu.com/api/trans/product/index)
- [有道翻译](https://ai.youdao.com/product-fanyi.s)
- 上述语言翻译服务商比较常用，其他短信如有需要可联系[Shadow](http://wpa.qq.com/msgrd?v=3&uin=1527200768&site=qq&menu=yes)集成
- 如需支持其他语言服务商，可以自行Fork，在`src/Sms.php`中添加对应的语言发送方法即可
- 语言快速集成（参考[《宏观设计模式》](docs/README.md) — 鸿宇科技出品）

## Installation

This package can be used in Laravel 5.6 or higher and needs PHP 7.2 or higher.

You can install the package via composer:

```bash
composer require hongyukeji/laravel-translate

composer require --dev hongyukeji/laravel-translate dev-master
```

## Config

After installation publish the config file:

```bash
php artisan vendor:publish --provider="Hongyukeji\LaravelTranslate\TranslateServiceProvider"
```

You can specify your source language, the target language(s), the translator and the path to your language files in there.

## Translators

| Name                  | Free | File                                                    | Documentation                       | Available languages |
|-----------------------|------|---------------------------------------------------------|-------------------------------------|----------|
| Google Translate HTTP | Yes  | Ben182\AutoTranslate\Translators\SimpleGoogleTranslator | /                                   | Over 100 |
| Deepl API v2          | No   | Ben182\AutoTranslate\Translators\DeeplTranslator        | [Documentation](https://www.deepl.com/docs-api.html) | EN, DE, FR, ES, PT, IT, NL, PL, RU |

If you have lots of translations to make I recommend Deepl. It is fast, reliable and you will not encounter any rate limiting.

## Usage

### Missing translations

Simply call the artisan missing command for translating all the translations that are set in your source language, but not in your target language:

```bash
php artisan translate:missing
```

E.g. you have English set as your source language. The source language has translations in auth.php:

```php
<?php

return [
    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',
];
```

Your target language is German. The auth.php file has the following translations:

```php
<?php

return [
    'failed' => 'Diese Kombination aus Zugangsdaten wurde nicht in unserer Datenbank gefunden.',
];
```

The artisan missing command will then translate the missing `auth.throttle` key.

### All translations

To overwrite all your existing target language keys with the translation of the source language simply call:

```bash
php artisan translate:all
```

This will overwrite every single key with a translation of the equivalent source language key.

### Parameters

Sometimes you have translations like these:

```php
'welcome' => 'Welcome, :name',
```

They can be replaced with variables. When we pass these placeholders to a translator service, weird things can happen. Sometimes the placeholder comes back in upper-case letters or it has been translated. Thankfully the package will respect your variable placeholders, so they will be the same after the translation.

## Extending

You can create your own translator by creating a class that implements `\Ben182\AutoTranslate\Translators\TranslatorInterface`. Simply reference it in your config file.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email moin@benjaminbortels.de instead of using the issue tracker.

## Credits

- [Benjamin Bortels](https://github.com/ben182)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
