# OXID Academy: Training extensions

This package contains an example module intended for training purposes at OXID Academy.

# Installation instructions

## Register and require the package

In the local development environment, cd into the root directory of your compilation (where composer.json and composer.lock
are located) and run Composer as shown below:
 
```
composer config repositories.oxid-academy/oxcoin vcs https://github.com/oxid-academy/oxcoin.git
```

This should result in the following change to `composer.json`:
```
   ...
  "repositories": {
    "oxid-academy/oxcoin": {
      "type": "vcs",
      "url": "https://github.com/oxid-academy/oxcoin.git"
    },
    ...
  },
  ...
```

And then, ...
```
composer require oxid-academy/oxcoin
```

... which makes it:
```
  "require": {
    ...,
    "oxid-academy/oxcoin": "dev-master"
  },
```

# Enabling the module

* When Composer is done processing your new request, you may enable the module:
```
vendor/bin/oe-console o:m:activate oxacoxcoin
```

# Running tests
cd into the root dir of your compilation and run the following command whereupon `/var/www/oxideshop` represents the absolute
path to your compilation:

```
cd /var/www/oxideshop/
`./vendor/bin/runtests /var/www/oxideshop/source/modules/oxac/oxcoin/Tests/`
