# laravel-wheres

A laravel wheres. Inspired by medoo

## Requirements

- laravel >=5.5


## Installation

```sh
$ composer require chastephp/laravel-wheres
```

## Usage

```php
User::query()->wheres([
    "user_id[!]" => 200,
])->get();
//WHERE user_id != 200
```

