<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project;

use Bitter\SimpleSupportSystem\Entity\Search\SavedProjectSearch;
use Bitter\SimpleSupportSystem\ProjectList;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\DefaultSet;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Available;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\ColumnSet;
use Bitter\SimpleSupportSystem\Search\Project\Result\Result;
use Concrete\Core\Search\AbstractSearchProvider;
use Concrete\Core\Search\Field\ManagerFactory;

class SearchProvider extends AbstractSearchProvider
{
    public function getFieldManager()
    {
        return ManagerFactory::get('project');
    }
    
    public function getSessionNamespace()
    {
        return 'project';
    }
    
    public function getCustomAttributeKeys()
    {
        return [];
    }
    
    public function getBaseColumnSet()
    {
        return new ColumnSet();
    }
    
    public function getAvailableColumnSet()
    {
        return new Available();
    }
    
    public function getCurrentColumnSet()
    {
        return ColumnSet::getCurrent();
    }
    
    public function createSearchResultObject($columns, $list)
    {
        return new Result($columns, $list);
    }
    
    public function getItemList()
    {
        return new ProjectList();
    }
    
    public function getDefaultColumnSet()
    {
        return new DefaultSet();
    }
    
    public function getSavedSearch()
    {
        return new SavedProjectSearch();
    }
}
