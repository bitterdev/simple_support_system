<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\SinglePage\Dashboard\SimpleSupportSystem;

use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Request;
use Concrete\Core\Support\Facade\Url;
use Symfony\Component\HttpFoundation\Response;
use Bitter\SimpleSupportSystem\Entity\Project as ProjectEntity;
use Concrete\Package\SimpleSupportSystem\Controller\Element\Header\Project as HeaderController;
use DateTime;

class Projects extends DashboardPageController
{
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Request */
    protected $request;

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

    public function view()
    {
        $headerMenu = new HeaderController();
        $this->set('headerMenu', $headerMenu);
        /** @var \Concrete\Package\SimpleSupportSystem\Controller\Search\Project $searchProvider */
        $searchProvider = $this->app->make(\Concrete\Package\SimpleSupportSystem\Controller\Search\Project::class);
        $result = $searchProvider->getCurrentSearchObject();
        if (is_object($result)) {
            $this->set('result', $result);
        }
    }
}
