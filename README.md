# PHP ePub generator

PHPePub allows a php script to generate ePub Electronic books on the fly, and send them to the user as downloads.

PHPePub support most of the ePub 2.01 specification, and enough of the new ePub3 specification to make valid ePub 3 books as well.

The projects is also hosted on PHPClasses.org at the addresses:
http://www.phpclasses.org/package/6115

PHPePub is meant to be easy to use for small projects, and still allow for comples and complete e-books should the need arise.

The Zip.php class in this project originates from http://www.phpclasses.org/package/6110

or on Github: git://github.com/Grandt/PHPZip.git

See the examples for example usage. The php files have "some" doumentation in them in the form of Javadoc style function headers.

## Installation

### Import
Add this requirement to your `composer.json` file:
```json
    "grandt/phpepub": ">=4.0.1"
```

### Composer
If you already have Composer installed, skip this part.

[Packagist](https://packagist.org/), the main composer repository has a neat and very short guide.
Or you can look at the guide at the [Composer site](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).
 
The easiest for first time users, is to have the composer installed in the same directory as your composer.json file, though there are better options.

Run this from the command line:
```
php -r "readfile('https://getcomposer.org/installer');" | php
```

This will check your PHP installation, and download the `composer.phar`, which is the composer binary. This file is not needed on the server though.

Once composer is installed you can create the `composer.json` file to import this package.
```json
{
    "require": {
        "grandt/phpepub": ">=4.0.1",
        "php": ">=5.3.0"
    }
}
```

Followed by telling Composer to install the dependencies.
```
php composer.phar install
```

this will download and place all dependencies defined in your `composer.json` file in the `vendor` directory.

Finally, you include the `autoload.php` file in the new `vendor` directory.
```php
<?php
    require 'vendor/autoload.php';
    .
    .
    .
```

## TODO:
* The goal being to encompass the majority of the features in the ePub 2.0 and 3.0 specifications, except the Daisy type files.
* Add better handling of Reference structures.
* Improve handling of media types and linked files.
* A/V content is allowed, but definitely not recommended, and MUST have a fallback chain ending in a valid file. If no such chain is provided, the content should not be added.
* Documentation, no one reads it, but everyone complains if it is missing.
* Better examples to fully cover the capabilities of the EPub classes.
* more TODO's.
