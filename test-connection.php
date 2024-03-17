<?php

$db = mysqli_connect('127.0.0.1', 'admin', 'adminpassword', 'catalog46.mysql', 3306);
mysqli_query($db, 'CREATE DATABASE foobar');
$db->close();

$db = mysqli_connect('127.0.0.1', 'admin', 'adminpassword', 'catalog46.foobar', 3306);
print_r($db);

mysqli_query($db, 'CREATE TABLE IF NOT EXISTS test_table (num INT, name VARCHAR(255))');
$value = bin2hex(random_bytes(6));
mysqli_query($db, "INSERT INTO test_table (num, name) VALUES (1, \"{$value}\")");
$result = mysqli_query($db, 'SELECT * FROM test_table');
print_r(mysqli_fetch_all($result, MYSQLI_ASSOC));

// $db = mysqli_connect('127.0.0.1', 'root', 'rootpassword', null, 3306);
// print_r($db);
