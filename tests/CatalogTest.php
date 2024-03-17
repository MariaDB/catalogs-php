<?php

namespace Mariadb\CatalogsPHP\Tests;

use Mariadb\CatalogsPHP\Catalog;
use PHPUnit\Framework\TestCase;

class CatalogTest extends TestCase
{
    private $catalog;
    private $pdoMock;

    protected function setUp(): void
    {
        // Create a mock for the PDO class
        $this->pdoMock = $this->createMock(\PDO::class);

        // Inject the PDO mock into your Catalog class
        $this->catalog = new Catalog('localhost', 3306, 'root', '', null, $this->pdoMock);
    }

    public function testList()
    {
        // Mock the PDOStatement for the 'SHOW CATALOGS' query
        $pdoStatementMock = new PDOStatementMock([
            ['Catalog' => 'test1'],
            ['Catalog' => 'test2'],
        ]);

        // Configure the PDO mock to return the PDOStatement mock for the 'SHOW CATALOGS' query
        $this->pdoMock
            ->method('query')
            ->with('SHOW CATALOGS')
            ->willReturn($pdoStatementMock);

        // Test the show method
        $catalogs = $this->catalog->list();
        $this->assertIsArray($catalogs);
        $this->assertEquals([
            'test1' => 3306,
            'test2' => 3306,
        ], $catalogs);
    }

    public function testGetPort()
    {
        $port = $this->catalog->getPort('test');
        $this->assertEquals(3306, $port);
    }

    public function testCreate()
    {
        // Configure the PDO mock to return the PDOStatement mock for the 'SHOW CATALOGS' query
        $this->pdoMock
            ->method('query')
            ->will($this->returnCallback(function ($query) {
                if ($query === 'SHOW CATALOGS') {
                    return new PDOStatementMock([
                        ['Catalog' => 'test1'],
                        ['Catalog' => 'test2'],
                    ]);
                }

                if (str_starts_with($query, 'SELECT * FROM mysql.global_priv')) {
                    return new PDOStatementMock([
                        [
                            'Host' => 'host',
                            'User' => 'user',
                            'Priv' => 'everything',
                        ],
                    ]);
                }
            }));

        // Test the create method
        $port = $this->catalog->create('test');
        $this->assertEquals(3306, $port);
    }
}
