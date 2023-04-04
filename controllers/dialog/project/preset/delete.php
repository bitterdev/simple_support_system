<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project\Preset;

use Bitter\SimpleSupportSystem\Entity\Search\SavedProjectSearch;
use Concrete\Core\Permission\Key\Key;
use Concrete\Controller\Dialog\Search\Preset\Delete as PresetDelete;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;

class Delete extends PresetDelete
{
    protected function canAccess()
    {
        $user = new User();
        return $user->isRegistered();
    }
    
    public function on_before_render()
    {
        parent::on_before_render();
        
        // use core views (remove package handle)
        $viewObject = $this->getViewObject();
        $viewObject->setInnerContentFile(null);
        $viewObject->setPackageHandle(null);
        $viewObject->setupRender();
    }
    
    public function getSavedSearchEntity()
    {
        /** @var EntityManager $em */
        $em = $this->app->make(EntityManager::class);
        
        if (is_object($em)) {
            return $em->getRepository(SavedProjectSearch::class);
        }
        
        return null;
    }
}
