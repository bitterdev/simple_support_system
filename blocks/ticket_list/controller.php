<?php /** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Block\TicketList;

use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\Entity\Ticket;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class Controller extends BlockController
{
    protected $btTable = "btTicketList";
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function on_start()
    {
        parent::on_start();
        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
    }

    public function getBlockTypeDescription()
    {
        return t('Display a ticket list on your site.');
    }

    public function getBlockTypeName()
    {
        return t('Ticket List');
    }

    public function validate($args)
    {
        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($args);
        $validationService->addRequired("ticketDetailPageId");
        $validationService->addRequired("createTicketPageId");
        $validationService->test();

        return $validationService->getError();
    }

    public function add()
    {
        $this->set("ticketDetailPageId", 0);
    }

    /** @noinspection PhpUnused */
    public function action_filter_by_project($projectId)
    {
        $project = $this->entityManager->getRepository(Project::class)->findOneBy(["projectId" => $projectId]);

        if ($project instanceof Project) {
            $tickets = $this->entityManager->getRepository(Ticket::class)->findBy(["project" => $project, "approved" => true]);
            $this->set('project', $project);
            $this->set('tickets', $tickets);
        } else {
            return $this->responseFactory->notFound(t("Invalid Project id."));
        }
    }

    public function view()
    {
        $projects = $this->entityManager->getRepository(Ticket::class)->findBy(["approved" => true]);
        $this->set('tickets', $projects);
    }

}
