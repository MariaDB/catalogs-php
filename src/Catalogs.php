<?php

namespace Mariadb\CatalogsPHP;

class Catalog{

    /**
     * 
     * @param string $server
     * @param int $serverPort
     * @param string $dbUser
     * @param string $dbPass
     * @return void
     */
    public function __construct( protected $server = 'localhost', protected $serverPort = 3306, protected $dbUser = 'root', private $dbPass = '') {
        // Check the connection.
        // Check the maria DB version.
    }

    /**
     * Create a new catalog
     * 
     * @param string $catName The new Catalofg name.
     * @param string|null $catUser 
     * @param string|null $catPassword 
     * @param array|null $args 
     * @return int 
     */
    public function create( string $catName, string $catUser = null, string $catPassword=null, array $args=null): int{
        // Check if shell scripts are allowed to execute.
        // Might be restricted by the server.
        // Check if the Catalog name is valid.
        if ($this->show($catName)) {
            // Throw exeption.
        }
        // Basicly run:
        // mariadb-install-db --catalogs="list" --catalog-user=user --catalog-password[=password] --catalog-client-arg=arg

        $port = $this->getPort($catName);
        return $port;
    }

    /**
     * Get the port of a catalog.
     * @param string $catName Tha catalog name.
     * @return int
     */
    public function getPort(string $catName) :int {
        // TODO what query to run?
        return $port??0;
    }

    /**
     * Get all catalogs.
     * @return int[] Named array with cat name and port.
     */
    public function show() :array{
        // Get all catalogs.
        // TODO what query to run?
        // Should contain catalog name and port.
        // TODO what to return?
        return [
            'catalog1' => 3310,
            'catalog2' => 3311,
        ];
    }

    /**
     * Drop a catalog.
     * @param string $catName The catalog name.
     * @return void 
     */
    public function drop( string $catName ) {
        // Drop the catalog.
        // TODO what query to run?
        // On error throw exception?
        return true;
    }

    public function alter() {
        // Out of scope
    }
}