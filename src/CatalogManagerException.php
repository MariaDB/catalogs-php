<?php

namespace Mariadb\CatalogsPHP;

/**
 * Exception handling class for Mariadb\CatalogsPHP.
 *
 * This class extends the base Exception class to provide custom exception
 * handling tailored to the needs of Mariadb\CatalogsPHP functionalities. It
 * allows for specifying an error message, an optional error code,
 * and a previous throwable to chain exceptions in a more detailed manner.
 *
 * @package Mariadb\CatalogsPHP
 */
class CatalogManagerException extends \Exception
{
    /**
     * Constructs the Exception.
     *
     * Initializes a new instance of the Exception class with an optional code
     * and a previous throwable for chaining exceptions. The $message parameter
     * is used to specify the error message, while the $code parameter is used
     * to specify an optional error code. If a previous throwable is provided
     * via the $previous parameter, it allows for exception chaining.
     *
     * @param string          $message  The Exception message to throw.
     * @param int             $code     The Exception code (optional).
     * @param \Throwable|null $previous The previous throwable used for exception chaining (optional).
     */
    public function __construct($message, $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
