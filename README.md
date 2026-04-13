# Font Downloader for TYPO3

Automatically downloads external CSS fonts and serves them locally. This improves GDPR compliance and page performance by eliminating third-party requests.

## Installation

```bash
composer require wapplersystems/font-downloader
```

## Requirements

- TYPO3 v14
- PHP 8.2+

## Usage

Simply include external font CSS via TypoScript as usual:

```typoscript
page.includeCSS {
  opensans = https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap
  fontawesome = https://use.fontawesome.com/releases/v5.15.4/css/all.css
}
```

The extension automatically intercepts these external URLs, downloads the CSS and all referenced font files, stores them locally in `typo3temp/assets/font-loader/`, and rewrites the references. No further configuration needed.

## Supported font providers

- **Google Fonts** — `fonts.googleapis.com/css`
- **Font Awesome** — `fontawesome.com/releases`

## Adding a custom font loader

You can add support for additional providers by implementing `FontLoaderInterface`:

```php
<?php

declare(strict_types=1);

namespace Vendor\MyExtension\FontLoader;

use WapplerSystems\FontDownloader\FontLoader\AbstractFontLoader;

class MyFontLoader extends AbstractFontLoader
{
    protected string $identifier = 'myprovider';

    public function isResponsible(string $url): bool
    {
        return str_contains($url, 'myprovider.com');
    }
}
```

Tag the service in `Configuration/Services.yaml`:

```yaml
services:
  Vendor\MyExtension\FontLoader\MyFontLoader:
    tags: ['fontloader']
```

The `load()` method is inherited from `AbstractFontLoader` and handles downloading automatically.

## Cache

Downloaded fonts are cached with infinite lifetime. To force a re-download:

```bash
vendor/bin/typo3 cache:flush --group=system
```

## License

GPL-2.0-or-later