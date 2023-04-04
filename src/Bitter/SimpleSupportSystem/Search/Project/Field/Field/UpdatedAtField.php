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

class UpdatedAtField extends AbstractField
{
    protected $requestVariables = [
        'updated_at_from_dt',
        'updated_at_from_h',
        'updated_at_from_m',
        'updated_at_from_a',
        'updated_at_to_dt',
        'updated_at_to_h',
        'updated_at_to_m',
        'updated_at_to_a',
    ];

    public function getKey()
    {
        return 'updatedAt';
    }

    public function getDisplayName()
    {
        return t('Updated At');
    }

    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var DateTime $dateTimeWidget */
        $dateTimeWidget = $app->make(DateTime::class);
        return $dateTimeWidget->datetime('updated_at_from', $dateTimeWidget->translate('updated_at_from', $this->data)) . t('to') . $dateTimeWidget->datetime('updated_at', $dateTimeWidget->translate('updated_at', $this->data));
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
        $dateFrom = $dateTimeWidget->translate('updated_at_from', $this->data);

        if ($dateFrom) {
            $list->filterByUpdatedAt($dateFrom, '>=');
        }

        $dateTo = $dateTimeWidget->translate('updated_at_to', $this->data);

        if ($dateTo) {
            if (preg_match('/^(.+\\d+:\\d+):00$/', $dateTo, $m)) {
                $dateTo = $m[1] . ':59';
            }

            $list->filterByUpdatedAt($dateTo, '<=');
        }
    }
}
