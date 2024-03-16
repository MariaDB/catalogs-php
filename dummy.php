<?php

// Creating a new catalog
$cat =  new Catalog();
$catPort = $cat->create( 'wp_1' );
// Create a DB in the collection.
// PDO create with $catPort
// Is that part of this class?
// Set DB_NAME with $catPort

class Catalog{

    public function __construct( private $server = 'localhost', private $serverPort = 3306, private $dbUser = 'root', private $dbPass = '') {

    }

    /**
     * @array $args  = --catalog-client-arg=arg
     */
    public function create( $catName, $catUser = null, $catPassword=null, array $args=null) {

        return $port; // ?
    }

    public function getPort($catName) {
        return $port;
    }

    // list all catalogs.
    public function show() {

    }

    public function drop( $catName ) {

    }

    public function alter() {
        //our of scope
    }
}