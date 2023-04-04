<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Result;

use Concrete\Core\Search\Result\Result as SearchResult;

class Result extends SearchResult
{
    public function getItemDetails($item)
    {
        return new Item($this, $this->listColumns, $item);
    }
    
    public function getColumnDetails($column)
    {
        return new Column($this, $column);
    }
}
