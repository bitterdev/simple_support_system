<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\ColumnSet\Column;

use Concrete\Core\Search\Column\Column;
use Concrete\Core\Search\Column\PagerColumnInterface;
use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\ProjectList;

class CreatedAtColumn extends Column implements PagerColumnInterface
{
    public function getColumnKey()
    {
        return 't0.createdAt';
    }

    public function getColumnName()
    {
        return t('Created At');
    }

    public function getColumnCallback()
    {
        return 'getCreatedAtDisplayValue';
    }

    /**
     * @param ProjectList $itemList
     * @param $mixed Project
     * @noinspection PhpDocSignatureInspection
     */
    public function filterListAtOffset(PagerProviderInterface $itemList, $mixed)
    {
        $query = $itemList->getQueryObject();
        $sort = $this->getColumnSortDirection() == 'desc' ? '<' : '>';
        $where = sprintf('t0.project_handle %s :project_handle', $sort);
        $query->setParameter('project_handle', $mixed->getProjectHandle());
        $query->andWhere($where);
    }
}
