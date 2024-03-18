<?php

namespace App\Models\Tenant;

use Spatie\Multitenancy\Models\Tenant;
use Mariadb\CatalogsPHP\CatalogManager;
/**
 * Class CatalogsTenantModel
 * @package App\Models\Tenant
 * @description Example of a tenant model used in a multi-tenancy application with MariaDB Catalogs
 */
class CatalogsTenantModel extends Tenant
{
    private static $catalog;
    
    protected static function booted()
    {
        static::creating(fn(CatalogsTenantModel $model) => $model->createCatalogs());
    }

    public function createCatalogs()
    {
        if (self::$catalog === null) {
            self::$catalog = new CatalogManager('localhost', 3306, 'root', 'rootPassword');
        }
        
        self::$catalog->create('test01');
    }
}