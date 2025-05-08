# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
ADDITIONAL
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version        |
|---------------|----------------|
| PHP           | 8.2, 8.3       |
| sylius/sylius | 2.0.x          |
| MySQL         | \>= 8.0        |
| NodeJS        | \>= 20.x, 22.x |

## CONFLICTS
As described in sylius/sylius-standard:2.0 (see CONFLICTS.md file) there is incompatibility  
between version of `behat/mink-selenium2-driver` used in Sylius 2.0 and this plugin.

Before startig the plugin installation, it is necessary to downgrade in `composer.json` version    
of `behat/mink-selenium2-driver` to `~1.6.0` and run `composer update behat/mink-selenium2-driver`.


## Composer:
```bash
composer require bitbag/dpd-pl-shipping-export-plugin --no-scripts
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    BitBag\DpdPlShippingExportPlugin\DpdPlShippingExportPlugin::class => ['all' => true]
];
```

Import required config in your `config/packages/_sylius.yaml` file:

```yaml
# config/packages/_sylius.yaml

imports:
    ...
    - { resource: "@DpdPlShippingExportPlugin/Resources/config/config.yml" }
```

Import routing in your `config/routes.yaml` file:
```yaml
# config/routes.yaml

bitbag_shipping_export_plugin:
    resource: "@BitBagSyliusShippingExportPlugin/Resources/config/routing.yml"
    prefix: /admin
```

### Update your database

After migration, please create a new diff migration and update database:
```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

### Clear application cache by using command:
```bash
bin/console cache:clear
```
**Note:** If you are running it on production, add the `-e prod` flag to this command.

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```
### Errors when attempting to export a shipment
- If non-existent postal codes are provided - both in the shipping gateway and during the order,
export of the shipment will not be possible.
- For the plugin to work properly, it is also necessary to add the weight of the products.
