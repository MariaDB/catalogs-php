<?php

namespace Mariadb\CatalogsPHP\Tests;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use PDOStatement;

class PDOStatementMock extends PDOStatement implements IteratorAggregate
{
    public function __construct(private array $data = [])
    {
    }

    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->data);
    }

    public function rowCount(): int
    {
        return count($this->data);
    }
}
