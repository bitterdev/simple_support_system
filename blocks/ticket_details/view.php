<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleSupportSystem\Entity\Ticket;
use Bitter\SimpleSupportSystem\Enumeration\TicketState;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Package\SimpleSupportSystem\Block\TicketDetails\Controller;
use Concrete\Core\Captcha\CaptchaInterface;

/** @var int $displayCaptcha */
/** @var Controller $controller */
/** @var Ticket $ticket */

$user = new User();
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var CaptchaInterface $captcha */
$captcha = $app->make(CaptchaInterface::class);
?>

<?php if (!$ticket instanceof Ticket) { ?>
    <p>
        <?php echo t("You need to select a ticket."); ?>
    </p>
<?php } else { ?>

    <?php if ($user->isSuperUser()) { ?>
        <div class="header-actions">
            <div class="btn-group pull-right">
                <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "resolve", $ticket->getTicketId()); ?>"
                   class="btn btn-primary">
                    <?php echo t("Resolve"); ?>
                </a>

                <button class="btn btn-default dropdown-toggle" type="button" id="workflowMenu" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="true">
                    <?php echo t("Workflow"); ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu" aria-labelledby="workflowMenu">
                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_OPEN); ?>">
                            <?php echo t("Open"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_ON_HOLD); ?>">
                            <?php echo t("On Hold"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_RESOLVED); ?>">
                            <?php echo t("Resolved"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_DUPLICATE); ?>">
                            <?php echo t("Duplicate"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_INVALID); ?>">
                            <?php echo t("Invalid"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_WONT_FIX); ?>">
                            <?php echo t("Won't fix"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "workflow", $ticket->getTicketId(), TicketState::TICKET_STATE_CLOSED); ?>">
                            <?php echo t("Closed"); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    <?php } ?>

    <div class="clearfix"></div>

    <?php if (isset($success)) { ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php } ?>

    <?php if (isset($error) && $error instanceof ErrorList && $error->has()) { ?>
        <div class="alert alert-danger">
            <?php /** @noinspection PhpDeprecationInspection */
            echo $error->output(); ?>
        </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-8">

            <div class="ticket-details">
                <h1 class="ticket-title">
                    <?php echo $ticket->getTitle(); ?>
                </h1>

                <p>
                    <strong>
                        <?php echo t("Ticket %s", $ticket->getTicketId()); ?>

                        <span class="badge">
                            <?php echo $ticket->getTicketStateDisplayValue(); ?>
                        </span>
                    </strong>
                </p>

                <p>
                    <?php echo t("%s created an ticket at %s", "<strong>" . $ticket->getAuthorDisplayValue() . "</strong>", $ticket->getCreatedAtDisplayValue()); ?>
                </p>

                <div class="ticket-content">
                    <?php echo $ticket->getContent(); ?>
                </div>

                <?php if (count($ticket->getAttachments()) > 0) { ?>
                    <div class="ticket-attachments">
                        <ul>
                            <?php foreach ($ticket->getAttachments() as $ticketAttachment) { ?>
                                <?php if ($ticketAttachment->getApprovedVersion() instanceof Version) { ?>
                                    <li>
                                        <a href="<?php echo $ticketAttachment->getApprovedVersion()->getDownloadURL(); ?>" target="_blank">
                                            <i class="fa fa-download"></i> <?php echo $ticketAttachment->getApprovedVersion()->getTitle(); ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>
            </div>

            <div class="ticket-comments">
                <?php if (count($ticket->getApprovedComments()) > 0) { ?>
                    <p class="ticket-comments-headline">
                        <strong>
                            <?php echo t2("%s Comment", "%s Comments", count($ticket->getApprovedComments())); ?>
                        </strong>
                    </p>

                    <?php foreach ($ticket->getApprovedComments() as $comment) { ?>
                        <div class="ticket-comment">
                            <p>
                                <?php echo t("%s created an comment at %s", "<strong>" . $comment->getAuthorDisplayValue() . "</strong>", $comment->getCreatedAtDisplayValue()); ?>
                            </p>

                            <div class="ticket-comment-content">
                                <?php echo $comment->getContent(); ?>
                            </div>

                            <div class="ticket-comment-actions">
                                <?php if ($comment->hasPermissions()) { ?>
                                    <a href="<?php echo (string)Url::to(Page::getCurrentPage(), "remove_comment", $comment->getTicketCommentId()); ?>">
                                        <?php echo t("Delete"); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if ($ticket->getTicketState() !== TicketState::TICKET_STATE_CLOSED) { ?>
                    <div class="ticket-comment-compose">
                        <?php if ($user->isRegistered()) { ?>
                            <form action="<?php echo (string)Url::to(Page::getCurrentPage(), "add_comment", $ticket->getTicketId()); ?>"
                                  method="post">

                                <div class="form-group">
                                    <?php echo $form->label("comment", t("Comment")); ?>
                                    <?php echo $editor->outputStandardEditor("comment"); ?>
                                </div>

                                <?php if ((int)$displayCaptcha === 1) { ?>
                                    <div class="form-group captcha">
                                        <?php $captchaLabel = $captcha->label(); ?>

                                        <?php if (!empty($captchaLabel)) { ?>
                                            <?php echo $form->label('', $captcha->label()); ?>
                                        <?php } ?>

                                        <div>
                                            <?php $captcha->display(); ?>
                                        </div>

                                        <div>
                                            <?php $captcha->showInput(); ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <button type="submit" class="btn btn-primary">
                                    <?php echo t("Add Comment"); ?>
                                </button>
                            </form>
                        <?php } else { ?>
                            <a href="<?php echo (string)Url::to('/login'); ?>" class="btn btn-primary">
                                <?php echo t("Login to comment"); ?>
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="alert alert-warning">
                <p>
                    <strong>
                        <?php echo t("Type"); ?>
                    </strong>

                    <br>

                    <?php echo $ticket->getTicketTypeDisplayValue(); ?>
                </p>

                <p>
                    <strong>
                        <?php echo t("Priority"); ?>
                    </strong>

                    <br>

                    <?php echo $ticket->getTicketPriorityDisplayValue(); ?>
                </p>

                <p>
                    <strong>
                        <?php echo t("State"); ?>
                    </strong>

                    <br>

                    <?php echo $ticket->getTicketStateDisplayValue(); ?>
                </p>
            </div>
        </div>
    </div>
<?php } ?>
