# Stand With Ukraine

[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

----

# Database for PHP-ISOCODES library

This repository contains only database of ISO country (ISO 3166-1), subdivision (ISO 3166-2), language (ISO 639-3), currency (ISO 4217) and scripts (ISO 15924).
To get this database with localized values, see [sokil/php-isocodes-db-i18n](https://github.com/sokil/php-isocodes-db-i18n).

[![Latest Stable Version](https://poser.pugx.org/sokil/php-isocodes-db-only/v/stable.png)](https://packagist.org/packages/sokil/php-isocodes-db-only)
[![Total Downloads](http://img.shields.io/packagist/dt/sokil/php-isocodes-db-only.svg?1)](https://packagist.org/packages/sokil/php-isocodes-db-only)
[![Daily Downloads](https://poser.pugx.org/sokil/php-isocodes-db-only/d/daily)](https://packagist.org/packages/sokil/php-isocodes-db-only/stats)

:1234: Database version: v4.16.0-33-g39f27a4f from 2024-02-02 04:16

Database updated at 2-nd day of every month.

## PHP-ISOCODES library

Details about library you may find in [sokil/php-isocodes](https://github.com/sokil/php-isocodes) repository.

## Installation

To install "sokil/php-isocodes" library with full database and i18n files:

```
composer require sokil/php-isocodes sokil/php-isocodes-db-i18n
```

You may also install "sokil/php-isocodes" with only database (no i18n will be available):

```
composer require sokil/php-isocodes sokil/php-isocodes-db-only
```

Database and internationalisation files updated at 2-nd day of every month. If you prefer to update more often, you may
use just "sokil/php-isocodes" library and tune update of database and i18n by yourself:

```
composer require sokil/php-isocodes
```

To setup manual update please read [instruction](https://github.com/sokil/php-isocodes#library-with-manual-database-update).

