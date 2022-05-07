# laravel-wheres

A laravel wheres. Inspired by medoo

## Requirements

- laravel >=5.5


## Installation

```sh
composer require chastephp/laravel-wheres
```

## Usage

The SQL in the comments is based on MySQL

```php
User::query()->wheres([
    "email" => "foo@bar.com"
])->toSql();
// select * from `users` where `email` = ?

User::query()->wheres([
    "user_id[>]" => 200
])->toSql();
// select * from `users` where `user_id` > ?

User::query()->wheres([
    "user_id[>=]" => 200
])->toSql();
// select * from `users` where `user_id` >= ?


User::query()->wheres([
    "user_id[!]" => 200,
])->toSql();
// select * from `users` where `user_id` != ?


User::query()->wheres([
    "age[<>]" => [20, 50]
])->toSql();
// select * from `users` where `age` between ? and ?


User::query()->wheres([
    "birthday[<>]" => [date("Y-m-d", strtotime('-30 days')), date("Y-m-d")]
])->toSql();
// select * from `users` where `birthday` between ? and ?


User::query()->wheres([
    "birthday[><]" => [date("Y-m-d", strtotime('-30 days')), date("Y-m-d")]
])->toSql();
// select * from `users` where `birthday` not between ? and ?


User::query()->wheres([
    "OR" => [
        "user_id" => [2, 123],
        "email" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"]
    ]
])->toSql();
// select * from `users` where (`user_id` in (?, ?) or `email` in (?, ?, ?))


User::query()->wheres([
    "AND" => [
        "user_name[!]" => "foo",
        "user_id[!]" => 1024,
        "email[!]" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"],
        "city[!]" => null,
        "promoted[!]" => true
    ]
])->toSql();
// select * from `users` where (`user_name` != ? and `user_id` != ? and `email` not in (?, ?, ?) and `city` is not null and `promoted` != ?)
```

### Compound

```php
User::query()->wheres([
    "AND" => [
        "OR" => [
            "user_name" => "foo",
            "email" => "foo@bar.com"
        ],
        "password" => "12345"
    ]
])->toSql();
// select * from `users` where ((`user_name` = ? or `email` = ?) and `password` = ?)

User::query()->wheres([
    "AND #1" => [
        "OR #the first condition" => [
            "user_name" => "foo",
            "email" => "foo@bar.com"
        ],
        "OR #the second condition" => [
            "user_name" => "bar",
            "email" => "bar@foo.com"
        ]
    ]
])->toSql();
// select * from `users` where ((`user_name` = ? or `email` = ?) and (`user_name` = ? or `email` = ?))

User::query()->wheres([
    "nickname[~]#1" => '%foo%',
    "nickname[~]#2" => '%bar%',
])->toSql();
// select * from `users` where `nickname` like ? and `nickname` like ?

User::query()->wheres([
    'OR' => [
        "nickname[~]#1" => '%foo%',
        "nickname[~]#2" => '%bar%',
    ]
])->toSql();
// select * from `users` where (`nickname` like ? or `nickname` like ?)
```


### LIKE
```php
User::query()->wheres([
    "city[~]" => "%stan%"
])->toSql();
// select * from `users` where `city` like ?

User::query()->wheres([
    "city[!~]" => "%stan%"
])->toSql();
// select * from `users` where `city` not like ?
```


### Quick And/Or
```php
User::query()->wheres([
    "province|city[~]" => "%stan%"
])->toSql();
// select * from `users` where (`province` like ? or `city` like ?)

User::query()->wheres([
   "province&city[~]" => "%stan%"
])->toSql();
// select * from `users` where (`province` like ? and `city` like ?)
```
