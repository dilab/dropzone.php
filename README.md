# PHP backend for Dropzone.js


## Installation

To install, use composer:

``` composer require dilab/dropzone.php ```

## Usage

+ Dropzone::build()->name('test.jpg'')->upload($stream,$meta);

<aside class="notice">
Make sure you call `name()` before `upload()` for it to work.
</aside>

## Testing
```
$ ./vendor/bin/phpunit
```
