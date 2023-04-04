<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Result;

use Bitter\SimpleSupportSystem\Entity\Project;
use Concrete\Core\Search\Column\Set;
use Concrete\Core\Search\Result\Item as SearchResultItem;
use Concrete\Core\Search\Result\Result as SearchResult;

class Item extends SearchResultItem
{
    public $id;
    
    public function __construct(SearchResult $result, Set $columns, $item)
    {
        parent::__construct($result, $columns, $item);
        $this->populateDetails($item);
    }
    
    /**
    * @param Project $item
    */
    protected function populateDetails($item)
    {
        $this->id = $item->getProjectId();
    }
}
