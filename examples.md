# Examples

This readme file shows some examples on how to use `Catalog` class.

## Set up a connection with a MariaDB server

```php
$catalog = new Catalog( '127.0.0.1', 3306, 'user', 'password' );
```

## Create a catalog on MariaDB

```php
$catalog->create('catalog_name');
```

## Return a list of all existing catalogs

```php
$catalogs = $catalog->show();
/*
Returns an array of catalogs on MariaDB:
Array
(
    [cat1] => 3306
    [cat2] => 3306
    [cat3] => 3306
    [def] => 3306
)
*/
```

## Drop a catalog - it shouldn't have any databases besides mysql, sys, performance_schema and information_schema

```php
$catalog->drop('catalog_name');
```
