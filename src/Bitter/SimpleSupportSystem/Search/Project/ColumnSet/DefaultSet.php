<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\ColumnSet;

use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column\CreatedAtColumn;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column\IdColumn;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column\ProjectNameColumn;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column\ProjectHandleColumn;
use Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column\UpdatedAtColumn;


class DefaultSet extends ColumnSet
{
    protected $attributeClass = 'CollectionAttributeKey';
    
    public function __construct()
    {
        $this->addColumn(new IdColumn());
        $this->addColumn(new ProjectNameColumn());
        $this->addColumn(new ProjectHandleColumn());
        $this->addColumn(new CreatedAtColumn());
        $this->addColumn(new UpdatedAtColumn());
        
        $id = $this->getColumnByKey('t0.projectId');
        $this->setDefaultSortColumn($id, 'desc');
    }
}
