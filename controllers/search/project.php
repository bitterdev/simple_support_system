<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleSupportSystem\Controller\Search;

use Bitter\SimpleSupportSystem\Entity\Search\SavedProjectSearch;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Concrete\Controller\Search\Standard;
use Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project\AdvancedSearch;

class Project extends Standard
{
    /**
     * @return \Concrete\Controller\Dialog\Search\AdvancedSearch
     */
    protected function getAdvancedSearchDialogController()
    {
        return $this->app->make(AdvancedSearch::class);
    }

    /**
     * @param int $presetID
     *
     * @return SavedProjectSearch|null
     */
    protected function getSavedSearchPreset($presetID)
    {
        $em = $this->app->make(EntityManagerInterface::class);
        return $em->find(SavedProjectSearch::class, $presetID);
    }

    /**
     * @return KeywordsField[]
     */
    protected function getBasicSearchFieldsFromRequest()
    {
        $fields = parent::getBasicSearchFieldsFromRequest();
        $keywords = htmlentities($this->request->get('cKeywords'), ENT_QUOTES, APP_CHARSET);
        if ($keywords) {
            $fields[] = new KeywordsField($keywords);
        }

        return $fields;
    }

    /**
     * @return bool
     */
    protected function canAccess()
    {
        $user = new User();
        return $user->isRegistered() && $user->isSuperUser();
    }
}
