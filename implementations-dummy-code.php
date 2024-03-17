<?php

require ('vendor/autoload.php');
/****
 * Use case 1: Create a new catalog and connect it to a wp install.
 */
use Mariadb\CatalogsPHP\Catalog;

//$conn = mysqli_connect('127.0.0.1', 'root', 'rootpassword');

// Creating a new catalog
$cat =  new Catalog( "127.0.0.1", 3306, 'root', 'rootpassword');
$catPort = $cat->create( 'catalog33' );
$cat->createAdminUserForCatalog( 'catalog33', 'admin', 'adminpassword' );

//var_dump($cat->show());
// Using PDO, Create a DB and user in the collection using the $catPort.
// Set DB_NAME with the "name:$catPort"

