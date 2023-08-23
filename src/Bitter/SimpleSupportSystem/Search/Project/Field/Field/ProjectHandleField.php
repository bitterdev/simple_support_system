<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Field\Field;

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\Support\Facade\Application;
use Bitter\SimpleSupportSystem\ProjectList;

class ProjectHandleField extends AbstractField
{
    protected $requestVariables = [
        'projectHandle'
    ];
    
    public function getKey()
    {
        return 'projectHandle';
    }
    
    public function getDisplayName()
    {
        return t('Project Handle');
    }
    
    /**
     * @param ProjectList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        if (isset($this->data['projectHandle'])) {
            $list->filterByProjectHandle($this->data['projectHandle']);
        }
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('projectHandle', $this->data['projectHandle'] ?? null);
    }
}
