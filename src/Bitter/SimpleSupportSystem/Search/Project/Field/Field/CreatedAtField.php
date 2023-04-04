<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Field\Field;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\SimpleSupportSystem\ProjectList;

class CreatedAtField extends AbstractField
{
    protected $requestVariables = [
        'created_at_from_dt',
        'created_at_from_h',
        'created_at_from_m',
        'created_at_from_a',
        'created_at_to_dt',
        'created_at_to_h',
        'created_at_to_m',
        'created_at_to_a',
    ];

    public function getKey()
    {
        return 'createdAt';
    }

    public function getDisplayName()
    {
        return t('Created At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTimeWidget */
        $dateTimeWidget = $app->make(DateTime::class);
        return $dateTimeWidget->datetime('created_at_from', $dateTimeWidget->translate('created_at_from', $this->data)) . t('to') . $dateTimeWidget->datetime('created_at', $dateTimeWidget->translate('created_at', $this->data));
    }

    /**
     * @param ItemList|ProjectList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTimeWidget */
        $dateTimeWidget = $app->make(DateTime::class);
        $dateFrom = $dateTimeWidget->translate('created_at_from', $this->data);

        if ($dateFrom) {
            $list->filterByCreatedAt($dateFrom, '>=');
        }

        $dateTo = $dateTimeWidget->translate('created_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByCreatedAt($dateTo, '<=');
        }
    }
}
