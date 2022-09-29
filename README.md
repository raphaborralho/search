# Raphaborralho/search

---

Package made for XLR8 with PHP 8 and Laravel.

## Features

- Latitude and Longitude by parameters
- Order by price by night and proximity

## Installation

```shell
    composer install raphaborralho/search
    php artisan vendor:publish
````

## Usage

Edit and add multiple sources of data in array
```php
#config/search.php

return [
    //Array with multiples source
    'source' => [
        //'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_1.json',
        //'https://xlr8-interview-files.s3.eu-west-2.amazonaws.com/source_2.json',
    ]
];
```

## License

MIT
