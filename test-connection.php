<?php
// $db = new \PDO('mysql:host=localhost;dbname=cat2.foo;charset=utf8mb4;unix_socket=/var/run/mysqld/mysqld.sock', "sammy", "password");
// print_r($db);

$db = mysqli_connect('127.0.0.1', 'user1', 'password', 'cat1.testdb1', 3306);
print_r($db);
