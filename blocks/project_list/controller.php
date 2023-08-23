<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Block\ProjectList;

use Bitter\SimpleSupportSystem\Entity\Project;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Form\Service\Validation;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController
{
    protected $btTable = "btProjectList";
    /** @var EntityManagerInterface */
    protected $entityManager;
    protected $btExportPageColumns = ["ticketListPageId", "createTicketPageId"];

    public function on_start()
    {
        parent::on_start();
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    public function getBlockTypeDescription()
    {
        return t('Display a project list on your site.');
    }

    public function getBlockTypeName()
    {
        return t('Project List');
    }

    public function validate($args)
    {
        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($args);
        $validationService->addRequired("ticketListPageId");
        $validationService->addRequired("createTicketPageId");
        $validationService->test();

        return $validationService->getError();
    }

    public function add()
    {
        $this->set("ticketListPageId", 0);
        $this->set("createTicketPageId", null);
    }

    public function view()
    {
        $projects = [];

        /** @var Project[] $allProjects */
        $allProjects = $this->entityManager->getRepository(Project::class)->findAll();
        foreach($allProjects as $project) {
            if (count($project->getTickets()) > 0) {
                $projects[] = $project;
            }
        }

        $this->set('projects', $projects);
    }

}
