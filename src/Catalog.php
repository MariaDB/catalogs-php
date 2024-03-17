<?php

namespace Mariadb\CatalogsPHP;

/**
 * Manage MariaDB catalogs via php.
 *
 * @package Mariadb\CatalogsPHP
 */
class Catalog
{

    /**
     * The connection to the MariaDB server.
     *
     * @var \PDO
     */
    private $connection;

    public const MINIMAL_MARIA_VERSION = '11.0.3';
    // This is too low, because this is a beta version we are developing for.


    /**
     * Class constructor.
     *
     * Initializes a new instance of the class with database connection
     * details. Allows for specification of the server, server port,
     * database user, and password, as well as additional options for
     * the server.
     * Default values are provided to simplify instantiation for common scenarios.
     *
     * @param string     $dbHost    The hostname or IP address of the database server. Default is 'localhost'.
     * @param int        $dbPort    The TCP/IP port of the database server. Default is 3306.
     * @param string     $dbUser    The username for the database login. Default is 'root'.
     * @param string     $dbPass    The password for the database login. Default is an empty string.
     * @param array|null $dbOptions Optional. An array of options for the server connection.
     *
     * @throws PDOException If a PDO error occurs during the connection attempt.
     * @throws Exception    If a general error occurs during instantiation.
     */
    public function __construct(
        protected string $dbHost='localhost',
        protected int $dbPort=3306,
        protected string $dbUser='root',
        protected string $dbPass='',
        protected ?array $dbOptions=null
    ) {
        // Connect.
        try {
            // Corrected to use the updated parameter names.
            $this->connection = new \PDO(
                "mysql:host=$dbHost;port=$dbPort",
                $dbUser,
                $dbPass,
                $dbOptions
            );
        } catch (\PDOException $e) {
            throw $e;
        }

        // Check the MariaDB version.
        $versionQuery = $this->connection->query('SELECT VERSION()');
        $version      = $versionQuery->fetchColumn();

        if (version_compare($version, self::MINIMAL_MARIA_VERSION, '<') === true) {
            throw new Exception(
                'The MariaDB version is too low. The minimal version is '.self::MINIMAL_MARIA_VERSION
            );
        }

    }


    /**
     * Create a new catalog
     *
     * @param string      $catName     The new Catalog name.
     * @param string|null $catUser     The Catalog user
     * @param string|null $catPassword The Catalog user password
     * @param array|null  $args        Additional args
     *
     * @return int
     */
    public function create(
        string $catName,
        string $catUser=null,
        string $catPassword=null,
        array $args=null
    ): int {
        // Check if shell scripts are allowed to execute.
        // Might be restricted by the server.
        // Check if the Catalog name is valid.
        if (in_array($catName, array_keys($this->show())) === true) {
            throw new Exception('Catalog name already exists.');
        }

        // Basically run:
        // mariadb-install-db --catalogs="list" --catalog-user=user --catalog-password[=password] --catalog-client-arg=arg
        $cmd = 'mariadb-install-db --catalogs="'.escapeshellarg($catName).'" --catalog-user='.escapeshellarg($catUser).' --catalog-password='.escapeshellarg($catPassword);
        system($cmd);

        return $this->getPort($catName);

    }


    /**
     * Get the port of a catalog.
     *
     * @param string $catName The catalog name.
     *
     * @return int
     */
    public function getPort(string $catName): int
    {
        // TODO: wait for the functionality to be implemented in the server.
        return ($port ?? 0);

    }


    /**
     * Get all catalogs.
     *
     * @return int[] Named array with cat name and port.
     */
    public function show(): array
    {
        $catalogs = [];
        $results  = $this->connection->query('SHOW CATALOGS');
        foreach ($results as $row) {
            // For now, we just return the default port for all catalogs.
            $catalogs[$row['name']] = $this->dbPort;
        }

        return $catalogs;

    }


    /**
     * Drop a catalog.
     *
     * @param string $catName The catalog name.
     *
     * @return void
     *
     * @throws PDOException If a PDO error occurs during the catalog drop attempt.
     * @throws Exception    If a general error occurs during catalog drop.
     */
    public function drop(string $catName): bool
    {
        $this->connection->query(
            'DROP CATALOG '.$this->connection->quote($catName)
        );

        if ($this->connection->errorCode() === true) {
            throw new Exception(
                'Error dropping catalog: '.$this->connection->errorInfo()[2]
            );
        }

        return true;

    }


    /**
     * This is out of scope, that's why it's private.
     * And it should be made public when implemented.
     *
     * @return void
     */
    private function alter()
    {
        // PHPCS:ignore
        // TODO implement the ALTER CATALOG command.

    }


}
