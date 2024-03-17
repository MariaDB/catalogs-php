<?php

use Mariadb\CatalogsPHP\Catalog;

/**
 * Use case 1: Create a new catalog and connect it to a wp install.
 */
$cat = new Catalog();
$catPort = $cat->create('wp_1');
// Using PDO, Create a DB and user in the collection using the $catPort.
// Set DB_NAME with the "name:$catPort"
