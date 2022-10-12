# xdg/locale

[![codecov](https://codecov.io/gh/php-xdg/locale/branch/main/graph/badge.svg?token=QE672UK2ZG)](https://codecov.io/gh/php-xdg/locale)

PHP library to work with POSIX locale identifiers.

## Installation

```sh
composer require xdg/locale
```

## Usage

### Constructing locale objects

```php
use Xdg\Locale\Locale;

// from a POSIX locale identifier
$locale = Locale::of('ca_ES@valencia');
// from an IETF (BCP47) language tag
$locale = Locale::of('zh-Hans-CN');
// On Windows, windows-specific locale identifiers are accepted
$locale = Locale::of('French_France.1252');
```

### Locale matching

Locale matching is performed using the algorithm described in the
[XDG Desktop Entry specification](https://specifications.freedesktop.org/desktop-entry-spec/desktop-entry-spec-latest.html#idm46403733864464).

```php
use Xdg\Locale\Locale;

$locale = Locale::of('fr_FR.UTF-8')
$locale->matches('fr'); // => true
$locale->matches('fr_BE'); // => false
```

Use `Locale::select()` to select the best match from a list of candidates.

```php
use Xdg\Locale\Locale;

$locale = Locale::of('fr_FR.UTF-8')
$locale->select(['fr_CA', 'fr_BE', 'fr']); // => 'fr'
```
