<?php /** @noinspection PhpUnused */
/** @noinspection DuplicatedCode */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\API\V1;

use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\Entity\Ticket;
use Bitter\SimpleSupportSystem\Enumeration\TicketPriority;
use Bitter\SimpleSupportSystem\Enumeration\TicketState;
use Bitter\SimpleSupportSystem\Enumeration\TicketType;
use Bitter\SimpleSupportSystem\Events\TicketCreate;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use /** @noinspection PhpDeprecationInspection */
    Concrete\Core\File\Importer;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Service;
use Concrete\Core\Page\Page;
use Concrete\Core\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;
use DateTime;

class Tickets implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $entityManager;
    protected $request;
    protected $ticketTypes = [];
    protected $ticketPriorities = [];
    protected $validation;
    protected $responseFactory;
    protected $config;
    protected $mailService;
    protected $eventDispatcher;
    protected $loggerFactory;
    protected $logger;

    public function __construct(
        EntityManager $entityManager,
        Request $request,
        Validation $validation,
        ResponseFactory $responseFactory,
        \Concrete\Core\Site\Service $siteService,
        Service $mailService,
        EventDispatcher $eventDispatcher,
        LoggerFactory $loggerFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->request = $request;
        $this->validation = $validation;
        $this->responseFactory = $responseFactory;
        $this->config = $siteService->getSite()->getConfigRepository();
        $this->mailService = $mailService;
        $this->eventDispatcher = $eventDispatcher;
        $this->loggerFactory = $loggerFactory;
        $this->logger = $this->loggerFactory->createLogger('simple_support_system');

        $this->ticketTypes = [
            TicketType::TICKET_TYPE_BUG => t("Bug"),
            TicketType::TICKET_TYPE_ENHANCEMENT => t("Enhancement"),
            TicketType::TICKET_TYPE_PROPOSAL => t("Proposal"),
            TicketType::TICKET_TYPE_TASK => t("Task")
        ];

        $this->ticketPriorities = [
            TicketPriority::TICKET_STATE_TRIVIAL => t("Trivial"),
            TicketPriority::TICKET_STATE_MINOR => t("Minor"),
            TicketPriority::TICKET_STATE_MAJOR => t("Major"),
            TicketPriority::TICKET_STATE_CRITICAL => t("Critical"),
            TicketPriority::TICKET_STATE_BLOCKER => t("Blocker")
        ];
    }

    public function create()
    {
        $editResponse = new EditResponse();
        $errorList = new ErrorList();
        $user = new User();

        $this->validation->setData($this->request->request->all());

        if (!$this->request->request->has("projectHandle")) {
            $this->validation->addRequired("projectId", t("You need to select a project."));
        }

        $this->validation->addRequired("title", t("You need to enter a title."));
        $this->validation->addRequired("content", t("You need to enter a content."));
        $this->validation->addRequired("ticketType", t("You need to select a type."));
        $this->validation->addRequired("ticketPriority", t("You need to select a priority."));

        if (!$user->isRegistered()) {
            $this->validation->addRequired("email", t("You need to enter a valid email address."));
        }

        if ($this->validation->test()) {
            if (!in_array($this->request->request->get("ticketType"), array_keys($this->ticketTypes))) {
                $errorList->add(t("The given type is invalid."));
            } else if (!in_array($this->request->request->get("ticketPriority"), array_keys($this->ticketPriorities))) {
                $errorList->add(t("The given priority is invalid."));
            } else {
                if (!$errorList->has()) {
                    if ($this->request->request->has("projectHandle")) {
                        $project = $this->entityManager->getRepository(Project::class)->findOneBy([
                            "projectHandle" => $this->request->request->get("projectHandle")
                        ]);
                    } else {
                        $project = $this->entityManager->getRepository(Project::class)->findOneBy([
                            "projectId" => (int)$this->request->request->get("projectId")
                        ]);
                    }

                    if ($project instanceof Project) {
                        $ticket = new Ticket();

                        $ticket->setApproved((int)$this->config->get('simple_support_system.enable_moderation') !== 1);
                        $ticket->setProject($project);
                        $ticket->setTitle($this->request->request->get("title"));
                        $ticket->setContent($this->request->request->get("content"));

                        if ($user->isRegistered()) {
                            $ticket->setAuthor($user->getUserInfoObject()->getEntityObject());
                            $ticket->setEmail($user->getUserInfoObject()->getUserEmail());
                        } else {
                            $ticket->setEmail($this->request->request->get("email"));
                        }

                        $importedFiles = new ArrayCollection();

                        if ($this->request->files->has("ticketAttachment")) {
                            foreach ($this->request->files->get("ticketAttachment") as $file) {
                                if ($file instanceof UploadedFile) {
                                    /** @var Importer $fileImporter */
                                    /** @noinspection PhpDeprecationInspection */
                                    $fileImporter = $this->app->make(Importer::class);

                                    try {
                                        /** @noinspection PhpDeprecationInspection */
                                        $importedFileVersion = $fileImporter->import($file->getPathname(), $file->getClientOriginalName());

                                        if ($importedFileVersion instanceof Version) {
                                            $importedFile = $importedFileVersion->getFile();
                                            $importedFiles->add($importedFile);
                                        } else {
                                            /** @noinspection PhpDeprecationInspection */
                                            /** @noinspection PhpParamsInspection */
                                            $errorList->add($fileImporter->getErrorMessage($fileImporter));
                                        }
                                    } catch (Exception $e) {
                                        $errorList->add($e);
                                    }
                                } else {
                                    $errorList->add(t("Malformed request."));
                                }
                            }
                        }

                        if (!$errorList->has()) {
                            $ticket->setTicketType($this->request->request->get("ticketType"));
                            $ticket->setTicketPriority($this->request->request->get("ticketPriority"));
                            $ticket->setTicketState(TicketState::TICKET_STATE_NEW);
                            $ticket->setAttachments($importedFiles);
                            $ticket->setCreatedAt(new DateTime());
                            $ticket->setUpdatedAt(new DateTime());

                            $this->entityManager->persist($ticket);

                            try {
                                $this->entityManager->flush();
                            } catch (OptimisticLockException $e) {
                                $errorList->add($e);
                            }

                            $mailReceivers = [];

                            $notificationMailAddress = $this->config->get("simple_support_system.notification_mail_address");

                            if (filter_var($notificationMailAddress, FILTER_VALIDATE_EMAIL)) {
                                $mailReceivers[] = $notificationMailAddress;
                            }

                            if (filter_var($ticket->getEmail(), FILTER_VALIDATE_EMAIL)) {
                                if (!in_array($ticket->getEmail(), $mailReceivers)) {
                                    $mailReceivers[] = $ticket->getEmail();
                                }
                            }

                            if (count($mailReceivers) > 0) {
                                foreach ($mailReceivers as $mailReceiver) {
                                    $this->mailService->reset();
                                    $this->mailService->to($mailReceiver);
                                    $this->mailService->addParameter("ticket", $ticket);
                                    $this->mailService->addParameter("ticketDetailPage", Page::getByID($this->config->get("simple_support_system.ticket_detail_page")));
                                    $this->mailService->load("new_ticket", "simple_support_system");

                                    try {
                                        $this->mailService->sendMail();
                                    } catch (Exception $e) {
                                        $errorList->add(t("There was an error while sending the mail."));
                                        break;
                                    }
                                }
                            }

                            if (filter_var($notificationMailAddress, FILTER_VALIDATE_EMAIL) &&
                                (int)$this->config->get('simple_support_system.enable_moderation') === 1) {

                                $this->mailService->reset();
                                $this->mailService->to($notificationMailAddress);
                                $this->mailService->addParameter("ticket", $ticket);
                                $this->mailService->addParameter("ticketDetailPage", Page::getByID($this->config->get("simple_support_system.ticket_detail_page")));
                                $this->mailService->load("approve_ticket", "simple_support_system");

                                try {
                                    $this->mailService->sendMail();
                                } catch (Exception $e) {
                                    $errorList->add(t("There was an error while sending the mail."));
                                }
                            }
                        }

                        if (!$errorList->has()) {
                            $ticketCreateEvent = new TicketCreate();
                            $ticketCreateEvent->setTicket($ticket);
                            $this->eventDispatcher->dispatch( $ticketCreateEvent, "on_create_ticket");

                            $this->logger->info(t("Ticket %s was successfully created.", $ticket->getTicketId()));
                        }
                    } else {
                        $errorList->add(t("The given project id is invalid."));
                    }
                }
            }
        } else {
            $errorList = $this->validation->getError();
        }

        $editResponse->setError($errorList);

        return $this->responseFactory->json($editResponse);
    }
}