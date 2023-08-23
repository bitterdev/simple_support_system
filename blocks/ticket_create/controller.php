<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Block\TicketCreate;

use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\Entity\Ticket;
use Bitter\SimpleSupportSystem\Enumeration\TicketPriority;
use Bitter\SimpleSupportSystem\Enumeration\TicketState;
use Bitter\SimpleSupportSystem\Enumeration\TicketType;
use Bitter\SimpleSupportSystem\Events\TicketCreate;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use /** @noinspection PhpDeprecationInspection */
    Concrete\Core\File\Importer;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Service;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DateTime;
use Exception;

class Controller extends BlockController
{
    protected $btTable = "btTicketCreate";
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactory */
    protected $responseFactory;
    protected $ticketTypes = [];
    protected $ticketPriorities = [];
    protected $thankYouMessage = '';
    protected /** @noinspection PhpUnused */
        $submitText = '';
    protected $displayCaptcha = 0;
    protected $redirectCID = 0;
    /** @var Service */
    protected $mailService;
    /** @var Repository */
    protected $config;
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    /** @var LoggerFactory */
    protected $loggerFactory;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    protected $btExportPageColumns = ["redirectCID"];

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->mailService = $this->app->make(Service::class);
        $this->config = $this->app->make(Repository::class);
        $this->eventDispatcher = $this->app->make(EventDispatcherInterface::class);
        $this->loggerFactory = $this->app->make(LoggerFactory::class);
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

    public function getBlockTypeDescription()
    {
        return t('Display a create ticket form on your site.');
    }

    public function getBlockTypeName()
    {
        return t('Create Ticket');
    }

    public function add()
    {
        $this->set('submitText', t("Create Ticket"));
        $this->set('thankYouMessage', t("Your ticket has been successfully created."));
        $this->set('redirectCID', 0);
        $this->set('displayCaptcha', 1);
    }

    public function save($args)
    {
        $args["displayCaptcha"] = isset($args["displayCaptcha"]) ? 1 : 0;

        parent::save($args);
    }

    public function validate($args)
    {
        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($args);
        $validationService->addRequired("thankYouMessage", t("You need to enter a thank you message."));
        $validationService->addRequired("submitText", t("You need to enter a submit text."));
        $validationService->test();

        return $validationService->getError();
    }

    private function setDefaults()
    {
        $projectList = [];

        /** @var Project[] $projects */
        $projects = $this->entityManager->getRepository(Project::class)->findAll();

        foreach ($projects as $project) {
            $projectList[$project->getProjectId()] = $project->getProjectName();
        }

        $this->set('projectList', $projectList);
        $this->set('ticketTypes', $this->ticketTypes);
        $this->set('ticketPriorities', $this->ticketPriorities);
    }

    public function requireAsset()
    {
        parent::requireAsset();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $this->requireAsset('css', 'core/frontend/errors');

        if ((int)$this->displayCaptcha === 1) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $this->requireAsset('css', 'core/frontend/captcha');
        }
    }

    /** @noinspection PhpUnused */
    public function action_create_ticket()
    {
        $errorList = new ErrorList();
        $user = new User();

        $this->setDefaults();

        /** @var Validation $validationService */
        $validationService = $this->app->make(Validation::class);

        $validationService->setData($this->request->request->all());
        $validationService->addRequiredToken("ticket_create");
        $validationService->addRequired("projectId", t("You need to select a project."));
        $validationService->addRequired("title", t("You need to enter a title."));
        $validationService->addRequired("content", t("You need to enter a content."));
        $validationService->addRequired("ticketType", t("You need to select a type."));
        $validationService->addRequired("ticketPriority", t("You need to select a priority."));

        if (!$user->isRegistered()) {
            $validationService->addRequired("email", t("You need to enter a valid email address."));
        }

        if ($validationService->test()) {
            if (!in_array($this->request->request->get("ticketType"), array_keys($this->ticketTypes))) {
                $errorList->add(t("The given type is invalid."));
            } else if (!in_array($this->request->request->get("ticketPriority"), array_keys($this->ticketPriorities))) {
                $errorList->add(t("The given priority is invalid."));
            } else {
                if ((int)$this->displayCaptcha === 1) {
                    /** @var CaptchaInterface $captcha */
                    $captcha = $this->app->make(CaptchaInterface::class);
                    if (!$captcha->check()) {
                        $errorList->add(t("Invalid captcha code."));
                    }
                }

                if (!$errorList->has()) {
                    $project = $this->entityManager->getRepository(Project::class)->findOneBy([
                        "projectId" => (int)$this->request->request->get("projectId")
                    ]);

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
                            $this->entityManager->flush();

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

                            if (!$errorList->has()) {
                                $ticketCreateEvent = new TicketCreate();
                                $ticketCreateEvent->setTicket($ticket);
                                $this->eventDispatcher->dispatch("on_create_ticket", $ticketCreateEvent);

                                $this->logger->info(t("Ticket %s was successfully created.", $ticket->getTicketId()));

                                if ($ticket->isApproved()) {
                                    if ($this->redirectCID > 0) {
                                        return $this->responseFactory->redirect((string)Url::to(Page::getByID($this->redirectCID), "display_ticket", $ticket->getTicketId()), Response::HTTP_TEMPORARY_REDIRECT);
                                    } else {
                                        $this->set('success', $this->thankYouMessage);
                                    }
                                } else {
                                    $this->set("success", t("Thank you. We have received your ticket. To avoid spamming your ticket needs to be reviewed before it will published at this site."));
                                }
                            }
                        }
                    } else {
                        $errorList->add(t("The given project id is invalid."));
                    }
                }
            }
        } else {
            $errorList = $validationService->getError();
        }

        $this->set('error', $errorList);
    }

    /** @noinspection PhpUnused */
    public function action_filter_by_project($projectId = null)
    {
        $this->setDefaults();
        $this->set('projectId', (int)$projectId);
    }

    public function view()
    {
        $this->setDefaults();
    }

}
