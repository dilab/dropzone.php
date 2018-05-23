# PHP backend for Dropzone.js


## Installation

To install, use composer:

``` composer require dilab/dropzone.php ```

## API

+ Dropzone::build()->name('test.jpg'')->upload($stream,$meta);

> Make sure you call ``` name ``` before ``` upload ``` for it to work correctly.

## Testing
```
$ ./vendor/bin/phpunit
```

## Sample

```php
include "vendor/autoload.php";

use Dilab\Dropzone;

$stream = fopen($_FILES['file']['tmp_name'], 'r+');

$dropzone = new Dropzone(__DIR__);

$dropzone->name($_FILES['file']['name'])->upload($stream, $_POST);
```
