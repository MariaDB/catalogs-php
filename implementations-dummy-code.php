<?php

require ('vendor/autoload.php');

use Mariadb\CatalogsPHP\CatalogManager;

/**
 * Use case 1: Create a new catalog and connect it to a wp install.
 */
$cat = new CatalogManager('127.0.0.1', 3306, 'root', 'rootpassword');
$catPort = $cat->create('wp_2');
$cat->createAdminUserForCatalog('wp_2', 'admin', 'adminpassword', '%');

// Using PDO, Create a DB and user in the collection using the $catPort.
// Set DB_NAME with the "name:$catPort"
