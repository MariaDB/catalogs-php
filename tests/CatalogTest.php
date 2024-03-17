<?php

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

        // Mock the PDOStatement as well
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
        $pdoStatementMock->method('fetchColumn')->willReturn('11.0.3');

        // Configure the PDO mock to return the PDOStatement mock
        $this->pdoMock->method('query')->willReturn($pdoStatementMock);

        // Inject the PDO mock into your Catalog class
        $this->catalog = new Catalog('localhost', 3306, 'root', '', null, $this->pdoMock);
        //$this->setPrivateProperty($this->catalog, 'connection', $this->pdoMock);
    }

    private function setPrivateProperty($object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public function testShow()
    {
        // Mock the PDOStatement for the 'SHOW CATALOGS' query
        $pdoStatementMock = $this->createMock(\PDOStatement::class);
				$pdoStatementMock->method('fetchAll')->willReturn([['test']]);

        // Configure the PDO mock to return the PDOStatement mock for the 'SHOW CATALOGS' query
        $this->pdoMock->method('query')->with('SHOW CATALOGS')->willReturn($pdoStatementMock);

        // Test the show method
        $catalogs = $this->catalog->show();
        $this->assertIsArray($catalogs);
        $this->assertArrayHasKey('test', $catalogs);
    }
}
