<?php
/**
 * Test connection with a standard user
 */
$db = mysqli_connect('127.0.0.1', 'user1', 'password', 'cat1.testdb1', 3306);
print_r($db);

/**
 * Test connection with a root user
 */
$db = mysqli_connect('127.0.0.1', 'root', 'rootpassword', null, 3306);
print_r($db);
