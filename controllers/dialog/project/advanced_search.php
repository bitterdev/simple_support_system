<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project;

use Concrete\Controller\Dialog\Search\AdvancedSearch as AdvancedSearchController;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Search\Field\ManagerFactory;
use Concrete\Core\Entity\Search\SavedSearch;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Bitter\SimpleSupportSystem\Entity\Search\SavedProjectSearch;
use Bitter\SimpleSupportSystem\Search\Project\SearchProvider;

class AdvancedSearch extends AdvancedSearchController
{
    protected $supportsSavedSearch = true;

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

    public function getSearchProvider()
    {
        return $this->app->make(SearchProvider::class);
    }

    public function getSavedSearchEntity()
    {
        $em = $this->app->make(EntityManager::class);
        if (is_object($em)) {
            return $em->getRepository(SavedProjectSearch::class);
        }

        return null;
    }

    public function getFieldManager()
    {
        return ManagerFactory::get('project');
    }

    public function getSavedSearchBaseURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/search/project/preset', $search->getID());
    }

    public function getCurrentSearchBaseURL()
    {
        return Url::to('/ccm/system/search/project/current');
    }

    public function getBasicSearchBaseURL()
    {
        return Url::to('/ccm/system/search/project/basic');
    }

    public function getSavedSearchDeleteURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/project/advanced_search/preset/delete?presetID=' . $search->getID());
    }

    public function getSavedSearchEditURL(SavedSearch $search)
    {
        return (string) Url::to('/ccm/system/dialogs/project/advanced_search/preset/edit?presetID=' . $search->getID());
    }
}
