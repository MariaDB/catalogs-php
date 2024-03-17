<?php

namespace Mariadb\CatalogsPHP;

use PDOException;

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

    // This is too low, because this is a beta version we are developing for.
    public const MINIMAL_MARIA_VERSION = '11.0.2';


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
     * @param \PDO|null  $pdo       Optional. An existing PDO connection to use. Default is null.
     *
     * @throws PDOException If a PDO error occurs during the connection attempt.
     * @throws Exception    If a general error occurs during instantiation.
     */
    public function __construct(
        protected string $dbHost='localhost',
        protected int $dbPort=3306,
        protected string $dbUser='root',
        protected string $dbPass='',
        protected ?array $dbOptions=null,
        protected ?\PDO $pdo=null
    ) {
        // Connect.
        try {
            if ($pdo !== null) {
                $this->connection = $pdo;
                return;
            }

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
     * @param string $catName The new Catalog name.
     *
     * @return int
     */
    public function create(string $catName): int
    {
        // Check if the Catalog name is valid.
        if (in_array($catName, array_keys($this->show())) === true) {
            throw new Exception('Catalog name already exists.');
        }

        $rootPrivileges = $this->connection->query("SELECT * FROM mysql.global_priv WHERE User='{$this->dbUser}' AND Host='%';");

        $scripts = [
            'src/create_catalog_sql/mysql_system_tables.sql',
            'src/create_catalog_sql/mysql_performance_tables.sql',
            'src/create_catalog_sql/mysql_system_tables_data.sql',
            'src/create_catalog_sql/maria_add_gis_sp.sql',
            'src/create_catalog_sql/mysql_sys_schema.sql',
        ];
        $this->connection->exec('CREATE CATALOG IF NOT EXISTS '.$catName);
        $this->connection->exec('USE CATALOG '.$catName);

        $this->connection->exec('CREATE DATABASE IF NOT EXISTS mysql');
        $this->connection->exec('USE mysql');

        foreach ($scripts as $script) {
            $content = file_get_contents($script);

            $content = preg_replace(
                '/DELIMITER\s+(?:\$\$|;)/',
                '',
                $content
            );

            $content = preg_replace(
                '/\$\$/',
                ';',
                $content
            );

            $this->connection->exec($content);
        }

        if ($rootPrivileges->rowCount() > 0) {
            foreach ($rootPrivileges as $privilege) {
                $host = $privilege['Host'];
                $user = $privilege['User'];
                $priv = $privilege['Priv'];
                $this->connection->exec("INSERT INTO mysql.global_priv VALUES ('{$host}', '{$user}', '{$priv}');");
            }
        }

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
            $catalogs[$row['Catalog']] = $this->dbPort;
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

        try {
            // Enter the catalog.
            $this->connection->exec('USE CATALOG '.$catName);

            // Check if there are any tables besides mysql, sys, performance_schema and information_schema.
            $tables = $this->connection->query('SHOW DATABASES');
            foreach ($tables as $table) {
                if (in_array($table['Database'], ['mysql', 'sys', 'performance_schema', 'information_schema']) === false) {
                    throw new \Exception('Catalog is not empty');
                }
            }

            // Drop mysql, sys and performance_schema.
            $this->connection->exec('DROP DATABASE IF EXISTS mysql');
            $this->connection->exec('DROP DATABASE IF EXISTS sys');
            $this->connection->exec('DROP DATABASE IF EXISTS performance_schema');

            // Drop the catalog.
            $this->connection->exec('DROP CATALOG '.$catName);
        } catch (\PDOException $e) {
            throw new \Exception('Error dropping catalog: '.$e->getMessage());
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


    /**
     * Create admin user for a catalog
     *
     * @param string $catalog  The catalog name
     * @param string $userName The user name
     * @param string $password The user password
     * @param string $authHost The database host
     *
     * @return void
     */
    public function createAdminUserForCatalog(
        string $catalog,
        string $userName,
        string $password,
        string $authHost='localhost'
    ): void {
        $this->connection->exec("USE CATALOG {$catalog}");
        $this->connection->exec("USE mysql");

        $this->connection = new \PDO(
            "mysql:host={$this->dbHost};port={$this->dbPort};dbname={$catalog}.mysql",
            $this->dbUser,
            $this->dbPass,
            $this->dbOptions
        );

        $this->connection->prepare(
            "CREATE USER ?@? IDENTIFIED BY ?;"
        )->execute([$userName, $authHost, $password]);

        $this->connection->prepare(
            "GRANT ALL PRIVILEGES ON `%`.* TO ?@? IDENTIFIED BY ? WITH GRANT OPTION;"
        )->execute([$userName, $authHost, $password]);

    }


}
