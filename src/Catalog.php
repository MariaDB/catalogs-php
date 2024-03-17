<?php

namespace Mariadb\CatalogsPHP;

/**
 * Manage MariaDB catalogs via php.
 *
 * @package Mariadb\CatalogsPHP
 */
class Catalog{

    /**
     * The connection to the MariaDB server.
     * @var \PDO
     */
    private $connection;

    const MINIMAL_MARIA_VERSION = '11.0.3'; // This is too low, because this is a pre-alpha version we are devloping for.

    /**
     * 
     * @param string $server
     * @param int $serverPort
     * @param string $dbUser
     * @param string $dbPass
     * @param array $options
     * @return void
     * @throws PDOException 
     * @throws Exception 
     */
    public function __construct( protected $server = 'localhost', protected $serverPort = 3306, protected $dbUser = 'root', protected $dbPass = '', protected $server_options = null) {
        // Connect.
        try { 
            $this->connection = new \PDO("mysql:host=$server;port=$serverPort", $dbUser, $dbPass, $server_options);
        } catch (\PDOException $e) {
            throw $e;
        }

        // Check the maria DB version.
        $version_query = $this->connection->query('SELECT VERSION()');
        $version = $version_query->fetchColumn();

        if (version_compare($version, self::MINIMAL_MARIA_VERSION, '<')) {
            throw new Exception('The MariaDB version is too low. The minimal version is ' . self::MINIMAL_MARIA_VERSION);
        }
    }

    /**
     * Create a new catalog
     * 
     * @param string $catName The new Catalog name.
     * @param string|null $catUser 
     * @param string|null $catPassword 
     * @param array|null $args 
     * @return int 
     */
    public function create( string $catName, string $catUser = null, string $catPassword=null, array $args=null): int{
        // Check if shell scripts are allowed to execute.
        // Might be restricted by the server.
        // Check if the Catalog name is valid.
        if (in_array($catName, array_keys($this->show()))) {
            throw new Exception('Catalog name already exists.');
        }
        // Basicly run:
        // mariadb-install-db --catalogs="list" --catalog-user=user --catalog-password[=password] --catalog-client-arg=arg

        $cmd = 'mariadb-install-db --catalogs="' . escapeshellarg($catName) .
            '" --catalog-user=' . escapeshellarg($catUser) .
            ' --catalog-password=' . escapeshellarg($catPassword);
        system($cmd);

        return $this->getPort($catName);
    }

    /**
     * Get the port of a catalog.
     * @param string $catName Tha catalog name.
     * @return int
     */
    public function getPort(string $catName) :int {
        // TODO wait for the functionality to be implemented in the server.
        return $port??0;
    }

    /**
     * Get all catalogs.
     * @return int[] Named array with cat name and port.
     */
    public function show() :array
    {
        $catalogs = [];
        $results = $this->connection->query('SHOW CATALOGS');
        foreach ($results as $row)
        {
            // For now, we just return the default port for all catalogs.
            $catalogs[$row['name']] = $this->serverPort;
        }
        return $catalogs;
    }

    /**
     * Drop a catalog.
     * @param string $catName The catalog name.
     * @return void 
     */
    public function drop( string $catName ) : bool{

        $this->connection->query('DROP CATALOG ' .
            $this->connection->quote($catName));

        if ($this->connection->errorCode()) {
            throw new Exception('Error dropping catalog: ' . $this->connection->errorInfo()[2]);
        }
        return true;
    }

    /**
     * This is out of scope, that's why it's private.
     * And it should be made public when implemented.
     * 
     * @return void 
     */
    private function alter() {
        // TODO implement the ALTER CATALOG command.
    }
}