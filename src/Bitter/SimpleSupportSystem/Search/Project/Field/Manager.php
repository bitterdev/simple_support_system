<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Field;

use Bitter\SimpleSupportSystem\Search\Project\Field\Field\CreatedAtField;
use Bitter\SimpleSupportSystem\Search\Project\Field\Field\UpdatedAtField;
use Concrete\Core\Search\Field\Manager as FieldManager;
use Bitter\SimpleSupportSystem\Search\Project\Field\Field\ProjectNameField;
use Bitter\SimpleSupportSystem\Search\Project\Field\Field\ProjectHandleField;

class Manager extends FieldManager
{
    
    public function __construct()
    {
        $properties = [
            new ProjectNameField(),
            new ProjectHandleField(),
            new CreatedAtField(),
            new UpdatedAtField()
        ];
        $this->addGroup(t('Core Properties'), $properties);
    }
}
