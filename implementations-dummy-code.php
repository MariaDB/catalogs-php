<?php

require ('vendor/autoload.php');

use Mariadb\CatalogsPHP\Catalog;

/**
 * Use case 1: Create a new catalog and connect it to a wp install.
 */
$cat = new Catalog('127.0.0.1', 3306, 'root', 'rootpassword');
$catPort = $cat->create('wp_1');
$cat->createAdminUserForCatalog('wp_1', 'admin', 'adminpassword', '%');

// Using PDO, Create a DB and user in the collection using the $catPort.
// Set DB_NAME with the "name:$catPort"
