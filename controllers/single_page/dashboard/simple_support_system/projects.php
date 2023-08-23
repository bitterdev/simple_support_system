<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\SinglePage\Dashboard\SimpleSupportSystem;

use Bitter\SimpleSupportSystem\Entity\Search\SavedProjectSearch;
use Bitter\SimpleSupportSystem\Navigation\Breadcrumb\Dashboard\DashboardProjectsBreadcrumbFactory;
use Bitter\SimpleSupportSystem\Search\Project\Menu\MenuFactory;
use Bitter\SimpleSupportSystem\Search\Project\SearchProvider;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Support\Facade\Url;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\HttpFoundation\Response;
use Bitter\SimpleSupportSystem\Entity\Project as ProjectEntity;
use DateTime;

class Projects extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;

    /** @var Element */
    protected $headerMenu;
    /** @var Element */
    protected $headerSearch;

    /**
     * @return SearchProvider
     * @throws BindingResolutionException
     */
    protected function getSearchProvider(): SearchProvider
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(SearchProvider::class);
    }

    /**
     * @return QueryFactory
     * @throws BindingResolutionException
     */
    protected function getQueryFactory(): QueryFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(QueryFactory::class);
    }

    protected function getHeaderMenu(): Element
    {
        if (!isset($this->headerMenu)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerMenu = $this->app->make(ElementManager::class)->get('projects/search/menu', 'simple_support_system');
        }

        return $this->headerMenu;
    }

    protected function getHeaderSearch(): Element
    {
        if (!isset($this->headerSearch)) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $this->headerSearch = $this->app->make(ElementManager::class)->get('projects/search/search', 'simple_support_system');
        }

        return $this->headerSearch;
    }

    /**
     * @param Result $result
     * @throws BindingResolutionException
     */
    protected function renderSearchResult(Result $result)
    {
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerMenu->getElementController()->setQuery($result->getQuery());
        /** @noinspection PhpPossiblePolymorphicInvocationInspection */
        $headerSearch->getElementController()->setQuery($result->getQuery());

        $this->set('resultsBulkMenu', $this->app->make(MenuFactory::class)->createBulkMenu());
        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $this->setThemeViewTemplate('full.php');
    }

    /**
     * @param Query $query
     * @return Result
     * @throws BindingResolutionException
     */
    protected function createSearchResult(Query $query): Result
    {
        $provider = $this->app->make(SearchProvider::class);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));

        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    protected function getSearchKeywordsField()
    {
        $keywords = null;

        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
        }

        return new KeywordsField($keywords);
    }

    /**
     * @throws BindingResolutionException
     */
    public function advanced_search()
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider(), $this->request, Request::METHOD_GET
        );

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);
    }

    /**
     * @throws BindingResolutionException
     */
    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedProjectSearch::class, $presetID);

            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($query);
                $this->renderSearchResult($result);

                return;
            }
        }

        $this->view();
    }

    /**
     * @return DashboardProjectsBreadcrumbFactory
     * @throws BindingResolutionException
     */
    protected function createBreadcrumbFactory(): DashboardProjectsBreadcrumbFactory
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(DashboardProjectsBreadcrumbFactory::class);
    }

    public function view()
    {
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider(), [
            $this->getSearchKeywordsField()
        ]);

        $result = $this->createSearchResult($query);

        $this->renderSearchResult($result);

        $this->headerSearch->getElementController()->setQuery(null);
    }

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->request = $this->app->make(Request::class);
    }

    private function setDefaults($entry = null)
    {

        $this->set("entry", $entry);
        $this->render("/dashboard/simple_support_system/projects/edit");
    }

    public function removed()
    {
        $this->set("success", t('The project has been successfully removed.'));
        $this->view();
    }

    public function saved()
    {
        $this->set("success", t('The project has been successfully updated.'));
        $this->view();
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     * @param array $data
     * @return bool
     */
    public function validate($data = null)
    {
        /** @var Validation $formValidator */
        $formValidator = $this->app->make(Validation::class);
        $formValidator->setData($this->request->request->all());
        $formValidator->addRequired("projectName", t("You need to enter a valid project name."));
        $formValidator->addRequired("projectHandle", t("You need to enter a valid project handle."));

        if ($formValidator->test()) {
            return true;
        } else {
            $this->error = $formValidator->getError();
            return false;
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function add()
    {
        $entry = new ProjectEntity();

        if ($this->token->validate("save_project_entity")) {
            $data = $this->request->request->all();

            if ($this->validate($data)) {
                $entry->setProjectName($data["projectName"]);
                $entry->setProjectHandle($data["projectHandle"]);
                $entry->setCreatedAt(new DateTime());
                $entry->setUpdatedAt(new DateTime());

                $this->entityManager->persist($entry);
                $this->entityManager->flush();

                return $this->responseFactory->redirect(Url::to("/dashboard/simple_support_system/projects/saved"), Response::HTTP_TEMPORARY_REDIRECT);
            }
        }

        $this->setDefaults($entry);
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function edit($id = null)
    {
        /** @var ProjectEntity $entry */
        $entry = $this->entityManager->getRepository(ProjectEntity::class)->findOneBy([
            "projectId" => $id
        ]);

        if ($entry instanceof ProjectEntity) {
            if ($this->token->validate("save_project_entity")) {
                $data = $this->request->request->all();

                if ($this->validate($data)) {
                    $entry->setProjectName($data["projectName"]);
                    $entry->setProjectHandle($data["projectHandle"]);
                    $entry->setUpdatedAt(new DateTime());

                    $this->entityManager->persist($entry);
                    $this->entityManager->flush();

                    return $this->responseFactory->redirect(Url::to("/dashboard/simple_support_system/projects/saved"), Response::HTTP_TEMPORARY_REDIRECT);
                }
            }

            $this->setDefaults($entry);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }

    /**
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    public function remove($id = null)
    {
        /** @var ProjectEntity $entry */
        $entry = $this->entityManager->getRepository(ProjectEntity::class)->findOneBy([
            "projectId" => $id
        ]);

        if ($entry instanceof ProjectEntity) {
            $this->entityManager->remove($entry);
            $this->entityManager->flush();

            return $this->responseFactory->redirect(Url::to("/dashboard/simple_support_system/projects/removed"), Response::HTTP_TEMPORARY_REDIRECT);
        } else {
            $this->responseFactory->notFound(null)->send();
            $this->app->shutdown();
        }
    }
}
