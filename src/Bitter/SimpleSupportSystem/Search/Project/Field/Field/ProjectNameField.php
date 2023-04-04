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

class ProjectNameField extends AbstractField
{
    protected $requestVariables = [
        'projectName'
    ];
    
    public function getKey()
    {
        return 'projectName';
    }
    
    public function getDisplayName()
    {
        return t('Project Name');
    }
    
    /**
     * @param ProjectList $list
     * @noinspection PhpDocSignatureInspection
     */
    public function filterList(ItemList $list)
    {
        $list->filterByProjectName($this->data['projectName']);
    }
    
    public function renderSearchField()
    {
        $app = Application::getFacadeApplication();
        /** @var Form $form */
        $form = $app->make(Form::class);
        return $form->text('projectName', $this->data['projectName']);
    }
}
