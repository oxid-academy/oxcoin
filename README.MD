# OXID Academy: Training extensions

This package contains an example extension for training purposes at OXID Academy. 

## Installation instructions

### Register and require the package

In the local development environment, cd into the root directory of your compilation
and run Composer as follows:
 
```
composer config repositories.oxid-academy/oxcoin vcs https://github.com/oxid-academy/oxcoin.git
```

This should result in the following change to `composer.json`:
```
...
  "repositories": {
    ...,
    "oxid-academy/oxcoin": {
      "type": "vcs",
      "url": "https://github.com/oxid-academy/oxcoin.git"
    }
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

## Installation

Now, run Composer to build the compilation:
```
composer install
```

## Running tests
`./vendor/bin/runtests /var/www/oxideshop/source/modules/oxac/oxcoin/Tests/`
