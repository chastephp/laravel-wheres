# laravel-wheres

A laravel wheres. Inspired by medoo

## Requirements

- laravel >=5.5


## Installation

```sh
composer require chastephp/laravel-wheres
```

## Usage

```php
User::query()->wheres([
    "email" => "foo@bar.com"
])->get();
//  WHERE email = 'foo@bar.com'


User::query()->wheres([
    "user_id[>=]" => 200
])->get();
// WHERE user_id >= 200


User::query()->wheres([
    "user_id[!]" => 200,
])->get();
// WHERE user_id != 200


User::query()->wheres([
    "age[<>]" => [20, 50]
])->get();
// WHERE age NOT BETWEEN 20 AND 50


User::query()->wheres([
    "birthday[<>]" => [date("Y-m-d", mktime(0, 0, 0, 1, 1, 2015)), date("Y-m-d")]
])->get();
// WHERE ("birthday" BETWEEN '2015-01-01' AND '2017-01-01')


User::query()->wheres([
    "birthday[><]" => [date("Y-m-d", mktime(0, 0, 0, 1, 1, 2015)), date("Y-m-d")]
])->get();
// WHERE ("birthday" NOT BETWEEN '2015-01-01' AND '2017-01-01')


User::query()->wheres([
    "OR" => [
    		"user_id" => [2, 123, 234, 54],
    		"email" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"]
    	]
])->get();
// WHERE
// user_id IN (2,123,234,54) OR
// email IN ('foo@bar.com','cat@dog.com','admin@medoo.in')


User::query()->wheres([
   "AND" => [
   		"user_name[!]" => "foo",
   		"user_id[!]" => 1024,
   		"email[!]" => ["foo@bar.com", "cat@dog.com", "admin@medoo.in"],
   		"city[!]" => null,
   		"promoted[!]" => true
   	]
])->get();
// WHERE
// `user_name` != 'foo' AND
// `user_id` != 1024 AND
// `email` NOT IN ('foo@bar.com','cat@dog.com','admin@medoo.in') AND
// `city` IS NOT NULL
// `promoted` != 1

```


```php

User::query()->wheres([
   "AND" => [
   		"OR" => [
   			"user_name" => "foo",
   			"email" => "foo@bar.com"
   		],
   		"password" => "12345"
   	]
])->get();
// WHERE (user_name = 'foo' OR email = 'foo@bar.com') AND password = '12345'



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
])->get();
// WHERE (
// 	(
// 		"user_name" = 'foo' OR "email" = 'foo@bar.com'
// 	)
// 	AND
// 	(
// 		"user_name" = 'bar' OR "email" = 'bar@foo.com'
// 	)
// )
```


### LIKE
```php
User::query()->wheres([
   "city[~]" => "%stan%" 
])->get();
// WHERE "city" LIKE '%stan%'

User::query()->wheres([
   "city[!~]" => "%stan%" 
])->get();
// WHERE "city" NOT LIKE '%stan%'

```


