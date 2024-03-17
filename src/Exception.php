<?php

namespace Mariadb\CatalogsPHP;

/**
 * A simple Exception wrapper class for the CatalogsPHP package.
 *
 * @package Mariadb\CatalogsPHP
 */
Class Exception extends \Exception{
    public function __construct($message, $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
