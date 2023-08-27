<?php /** @noinspection DuplicatedCode */
/** @noinspection PhpInconsistentReturnPointsInspection */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Block\TicketDetails;

use Bitter\SimpleSupportSystem\Entity\Ticket;
use Bitter\SimpleSupportSystem\Entity\TicketComment;
use Bitter\SimpleSupportSystem\Enumeration\TicketState;
use Bitter\SimpleSupportSystem\Events\TicketCommentCreate;
use Bitter\SimpleSupportSystem\Events\TicketStateChange;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Http\Response;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Logging\LoggerFactory;
use Concrete\Core\Mail\Service;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Controller extends BlockController
{
    protected $btTable = "btTicketDetails";
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var ResponseFactory */
    protected $responseFactory;
    protected $displayCaptcha = 0;
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

    public function on_start()
    {
        parent::on_start();

        $this->responseFactory = $this->app->make(ResponseFactory::class);
        $this->entityManager = $this->app->make(EntityManagerInterface::class);
        $this->mailService = $this->app->make(Service::class);
        /** @var \Concrete\Core\Site\Service $siteService */
        $siteService = $this->app->make(\Concrete\Core\Site\Service::class);
        $this->config = $siteService->getSite()->getConfigRepository();
        $this->eventDispatcher = $this->app->make(EventDispatcherInterface::class);
        $this->loggerFactory = $this->app->make(LoggerFactory::class);
        $this->logger = $this->loggerFactory->createLogger('simple_support_system');
    }

    public function registerViewAssets($outputContent = '')
    {
        parent::registerViewAssets($outputContent);

        $this->requireAsset('css', 'font-awesome');
    }

    public function getBlockTypeDescription()
    {
        return t('Display ticket details on your site.');
    }

    public function getBlockTypeName()
    {
        return t('Ticket Details');
    }

    public function save($args)
    {
        $args["displayCaptcha"] = isset($args["displayCaptcha"]) ? 1 : 0;

        parent::save($args);
    }

    public function add()
    {
        $this->set('displayCaptcha', 1);
    }

    /** @noinspection PhpUnused */
    public function action_display_ticket($ticketId)
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(["ticketId" => $ticketId, "approved" => true]);

        if ($ticket instanceof Ticket) {
            $this->set('ticket', $ticket);
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket id."));
        }
    }


    /** @noinspection PhpUnused */
    public function action_approve_ticket_comment($ticketCommentId)
    {
        $ticketComment = $this->entityManager->getRepository(TicketComment::class)->findOneBy(["ticketCommentId" => $ticketCommentId]);

        if ($ticketComment instanceof TicketComment) {

            $ticketComment->setApproved(true);
            $this->entityManager->persist($ticketComment);
            $this->entityManager->flush();

            $this->set("success", t("The comment has been approved."));

            $this->action_display_ticket($ticketComment->getTicket()->getTicketId());
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket comment id."));
        }
    }

    /** @noinspection PhpUnused */
    public function action_approve_ticket($ticketId)
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(["ticketId" => $ticketId]);

        if ($ticket instanceof Ticket) {
            $ticket->setApproved(true);
            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            $this->set("success", t("The ticket has been approved."));

            $this->action_display_ticket($ticketId);
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket id."));
        }
    }

    /** @noinspection PhpUnused */
    public function action_remove_comment($ticketCommentId = null)
    {
        $ticketComment = $this->entityManager->getRepository(TicketComment::class)->findOneBy(["ticketCommentId" => $ticketCommentId]);

        if ($ticketComment instanceof TicketComment) {

            if ($ticketComment->hasPermissions()) {
                $this->entityManager->remove($ticketComment);
                $this->entityManager->flush();

                $this->logger->info(t("Comment was removed from ticket %s.", $ticketComment->getTicket()->getTicketId()));

                return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "display_ticket", $ticketComment->getTicket()->getTicketId()), Response::HTTP_TEMPORARY_REDIRECT);
            } else {
                return $this->responseFactory->forbidden(
                    (string)Url::to(Page::getCurrentPage(), "remove_comment", $ticketComment->getTicketCommentId())
                );
            }
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket comment id."));
        }
    }

    /** @noinspection PhpUnused */
    public function action_add_comment($ticketId = null)
    {
        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(["ticketId" => $ticketId, "approved" => true]);

        if ($ticket instanceof Ticket) {
            /** @var Validation $formValidator */
            $formValidator = $this->app->make(Validation::class);
            $errorList = new ErrorList();
            $user = new User();

            $formValidator->setData($this->request->request->all());
            $formValidator->addRequired("comment", t("You need to enter a comment."));

            if ($formValidator->test()) {
                $comment = (string)$this->request->request->get("comment");

                if (!$user->isRegistered()) {
                    return $this->responseFactory->forbidden(
                        (string)Url::to(Page::getCurrentPage(), "display_ticket", $ticketId)
                    );
                } else {
                    if ((int)$this->displayCaptcha === 1) {
                        /** @var CaptchaInterface $captcha */
                        $captcha = $this->app->make(CaptchaInterface::class);
                        if (!$captcha->check()) {
                            $errorList->add(t("Invalid captcha code."));
                        }
                    }

                    if (!$errorList->has()) {
                        $ticketComment = new TicketComment();
                        $ticketComment->setApproved((int)$this->config->get('simple_support_system.enable_moderation') !== 1);
                        $ticketComment->setAuthor($user->getUserInfoObject()->getEntityObject());
                        $ticketComment->setUpdatedAt(new DateTime());
                        $ticketComment->setCreatedAt(new DateTime());
                        $ticketComment->setTicket($ticket);
                        $ticketComment->setContent($comment);

                        $this->entityManager->persist($ticketComment);
                        $this->entityManager->flush();

                        $mailReceivers = [];

                        $notificationMailAddress = $this->config->get("simple_support_system.notification_mail_address");

                        if (filter_var($notificationMailAddress, FILTER_VALIDATE_EMAIL)) {
                            $mailReceivers[] = $notificationMailAddress;
                        }

                        if (!in_array($ticket->getEmail(), $mailReceivers)) {
                            $mailReceivers[] = $ticket->getEmail();
                        }

                        if (filter_var($user->getUserInfoObject()->getEntityObject()->getUserEmail(), FILTER_VALIDATE_EMAIL)) {
                            if (!in_array($user->getUserInfoObject()->getEntityObject()->getUserEmail(), $mailReceivers)) {
                                $mailReceivers[] = $user->getUserInfoObject()->getEntityObject()->getUserEmail();
                            }
                        }

                        foreach ($ticket->getComments() as $currentTicketComment) {
                            if (!in_array($currentTicketComment->getAuthor()->getUserEmail(), $mailReceivers)) {
                                $mailReceivers[] = $currentTicketComment->getAuthor()->getUserEmail();
                            }
                        }

                        if (count($mailReceivers) > 0) {
                            foreach ($mailReceivers as $mailReceiver) {
                                $this->mailService->reset();
                                $this->mailService->to($mailReceiver);
                                $this->mailService->addParameter("ticketComment", $ticketComment);
                                $this->mailService->addParameter("ticketDetailPage", Page::getByID($this->config->get("simple_support_system.ticket_detail_page")));
                                $this->mailService->load("new_comment", "simple_support_system");

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
                            $this->mailService->addParameter("ticketComment", $ticketComment);
                            $this->mailService->addParameter("ticketDetailPage", Page::getByID($this->config->get("simple_support_system.ticket_detail_page")));
                            $this->mailService->load("approve_comment", "simple_support_system");

                            try {
                                $this->mailService->sendMail();
                            } catch (Exception $e) {
                                $errorList->add(t("There was an error while sending the mail."));
                            }
                        }

                        if (!$errorList->has()) {
                            $ticketCommentCreateEvent = new TicketCommentCreate();
                            $ticketCommentCreateEvent->setTicketComment($ticketComment);
                            $this->eventDispatcher->dispatch( $ticketCommentCreateEvent, "on_create_ticket_comment");

                            $this->logger->info(t("Comment was added to ticket %s.", $ticket->getTicketId()));

                            if ($ticketComment->isApproved()) {
                                return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "display_ticket", $ticketId), Response::HTTP_TEMPORARY_REDIRECT);
                            } else {
                                $this->set("success", t("Thank you. We have received your comment. To avoid spamming your comment needs to be reviewed before it will published at this site."));
                            }
                        }
                    }
                }
            } else {
                $errorList = $formValidator->getError();
            }

            $this->set('error', $errorList);
            $this->set('ticket', $ticket);
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket id."));
        }
    }

    /** @noinspection PhpUnused */
    public function action_resolve($ticketId = null)
    {
        return $this->action_workflow($ticketId, TicketState::TICKET_STATE_RESOLVED);
    }

    /** @noinspection PhpUnused */
    public function action_workflow($ticketId = null, $ticketState = null)
    {
        $errorList = new ErrorList();

        $ticket = $this->entityManager->getRepository(Ticket::class)->findOneBy(["ticketId" => $ticketId, "approved" => true]);

        if ($ticket instanceof Ticket) {
            $ticket->setTicketState($ticketState);

            $this->entityManager->persist($ticket);
            $this->entityManager->flush();

            $ticketStateChangEvent = new TicketStateChange();
            $ticketStateChangEvent->setTicket($ticket);
            $this->eventDispatcher->dispatch( $ticketStateChangEvent, "on_ticket_state_change");

            $this->logger->info(t("State of ticket %s has been changed.", $ticket->getTicketId()));

            $mailReceivers = [];

            $notificationMailAddress = $this->config->get("simple_support_system.notification_mail_address");

            if (filter_var($notificationMailAddress, FILTER_VALIDATE_EMAIL)) {
                $mailReceivers[] = $notificationMailAddress;
            }

            if (!in_array($ticket->getEmail(), $mailReceivers)) {
                $mailReceivers[] = $ticket->getEmail();
            }

            foreach ($ticket->getComments() as $currentTicketComment) {
                if (!in_array($currentTicketComment->getAuthor()->getUserEmail(), $mailReceivers)) {
                    $mailReceivers[] = $currentTicketComment->getAuthor()->getUserEmail();
                }
            }

            if (count($mailReceivers) > 0) {
                foreach ($mailReceivers as $mailReceiver) {
                    $this->mailService->reset();
                    $this->mailService->to($mailReceiver);
                    $this->mailService->addParameter("ticket", $ticket);
                    $this->mailService->addParameter("ticketDetailPage", Page::getByID($this->config->get("simple_support_system.ticket_detail_page")));
                    $this->mailService->load("change_ticket_state", "simple_support_system");

                    try {
                        $this->mailService->sendMail();
                    } catch (Exception $e) {
                        $errorList->add(t("There was an error while sending the mail."));
                        break;
                    }
                }
            }

            if (!$errorList->has()) {
                return $this->responseFactory->redirect((string)Url::to(Page::getCurrentPage(), "display_ticket", $ticketId), Response::HTTP_TEMPORARY_REDIRECT);
            } else {
                $this->set('error', $errorList);
                $this->set('ticket', $ticket);
            }
        } else {
            return $this->responseFactory->notFound(t("Invalid ticket id."));
        }
    }
}
