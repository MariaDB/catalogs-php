<?php

namespace Mariadb\CatalogsPHP;

use PDOException;

class Catalog{

    /**
     * The connection to the MariaDB server.
     * @var \PDO
     */
    private $connection;

    const MINIMAL_MARIA_VERSION = '11.0.2'; // This is too low, because this is a beta version we are devloping for.

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
        // Might be restricted by the server.
        // Check if the Catalog name is valid.
        if (in_array($catName, array_keys($this->show()))) {
            // Wesley: I commented out this and added a 'CREATE CATALOG IF NOT EXISTS' because we have to run this a second time, because of a MariaDB server segfault/crash the first time. See below.
            // throw new Exception('Catalog name already exists.');
        }

        $scripts = [
            'src/create_catalog_sql/mysql_system_tables.sql',
            'src/create_catalog_sql/mysql_performance_tables.sql',
            'src/create_catalog_sql/mysql_system_tables_data.sql',
            'src/create_catalog_sql/maria_add_gis_sp.sql',
            'src/create_catalog_sql/mysql_sys_schema.sql',
        ];
        $this->connection->exec('CREATE CATALOG IF NOT EXISTS ' .$catName);
        // echo "using catalog";
        $this->connection->exec('USE CATALOG ' . $catName);

        $this->connection->exec('CREATE DATABASE IF NOT EXISTS mysql');
        $this->connection->exec('USE mysql');

        foreach ($scripts as $script) {

            echo "executing $script\n";

            $content = file_get_contents($script);

            // Uncommented stuff were first tries.
            // But apparently it suffices to just remove the DELIMITER statements.
            // And replace the remaining $$ with ;

            // $matches = [];
            // preg_match_all('/DELIMITER\s+\$\$(.*?)\$\$.*?DELIMITER\s+;/s', $content, $matches);

            // $index = 0;
            // foreach ($matches[1] as $match) {
            //     $sql = 'CREATE PROCEDURE TMP_PROCEDURE_' . ($index++) . '() BEGIN ' . $match . '; END;';
            //     echo "sql = $sql";
            //     $this->connection->exec($sql);
            // }

            // $index = 0;
            // $content = preg_replace_callback(
            //     '/(DELIMITER\s+\$\$(.*?)\$\$.*?DELIMITER\s+;)/s',
            //     function ($matches) use (&$index) {
            //         return 'CALL TMP_PROCEDURE_' . ($index++) . '();';
            //     },
            //     $content
            // );

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
        // Basicly run:
        // mariadb-install-db --catalogs="list" --catalog-user=user --catalog-password[=password] --catalog-client-arg=arg

        /*$cmd = 'mariadb-install-db --catalogs="' . escapeshellarg($catName) .
            '" --catalog-user=' . escapeshellarg($catUser) .
            ' --catalog-password=' . escapeshellarg($catPassword);
        system($cmd);*/

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
            $catalogs[$row['Catalog']] = $this->serverPort;
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

    public function alter() {
        // Out of scope
    }

    /**
     * @return void
     */
    public function createAdminUserForCatalog(string $catalog, string $userName, string $password, string $authHost = 'localhost'): void
    {

        $this->connection->prepare("USE CATALOG ". $catalog);
        $this->connection->prepare("CREATE USER ?@? IDENTIFIED BY ?;")->execute([$userName, $authHost, $password]);
        $this->connection->prepare("GRANT ALL PRIVILEGES ON *.* TO ?@? WITH GRANT OPTION;")->execute([$userName, $authHost]);
    }
}