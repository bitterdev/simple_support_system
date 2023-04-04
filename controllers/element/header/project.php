<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\Element\Header;

use Concrete\Core\Controller\ElementController;

class Project extends ElementController
{
    protected $pkgHandle = "simple_support_system";
    
    public function getElement()
    {
        return "header/project";
    }
}
