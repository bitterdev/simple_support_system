<?php

/**
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2021 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

/** @noinspection DuplicatedCode */
/** @noinspection PhpUnused */

namespace Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project\Bulk;

use Bitter\SimpleSupportSystem\Entity\Project;
use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use Concrete\Core\User\User;
use Concrete\Core\Support\Facade\Url;
use Doctrine\ORM\EntityManagerInterface;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/projects/bulk/delete';
    protected $projects = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->populateProjects();

        $this->set('projects', $this->projects);
        $this->set('excluded', $this->excluded);
    }

    private function populateProjects()
    {
        $projectIds = $this->request("item");

        if (is_array($projectIds) && count($projectIds) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            foreach($projectIds as $projectId) {
                $this->projects[] = $entityManager->getRepository(Project::class)->findOneBy(["projectId" => (int)$projectId]);
            }
        }
    }

    public function submit()
    {
        $r = new UserEditResponse();

        $this->populateProjects();

        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            $this->app->shutdown();
        }

        $count = 0;

        if (count($this->projects) > 0) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->app->make(EntityManagerInterface::class);

            $db = $entityManager->getConnection();

            $db->executeQuery("SET foreign_key_checks = 0");

            foreach ($this->projects as $project) {
                $entityManager->remove($project);
                $entityManager->flush();
            }

            $db->executeQuery("SET foreign_key_checks = 1");
        }

        $r->setMessage(t2('%s project deleted', '%s projects deleted', $count));
        $r->setTitle(t('Projects Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/simple_support_system/projects'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $user = new User();
        return $user->isSuperUser();
    }
}
