<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\ColumnSet;

use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\Search\Column\Set;
use Bitter\SimpleSupportSystem\Search\Project\SearchProvider;


class ColumnSet extends Set
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public static function getCurrent()
    {
        $app = Facade::getFacadeApplication();
        /** @var $provider SearchProvider */
        $provider = $app->make(SearchProvider::class);
        $query = $provider->getSessionCurrentQuery();
        
        if ($query) {
            return $query->getColumns();
        }
        
        return $provider->getDefaultColumnSet();
    }
}
