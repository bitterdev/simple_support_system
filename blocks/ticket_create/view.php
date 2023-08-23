<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Captcha\CaptchaInterface;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\User\User;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Package\SimpleSupportSystem\Block\TicketCreate\Controller;

/** @var Controller $controller */
/** @var array $projectList */
/** @var array $ticketTypes */
/** @var array $ticketPriorities */
/** @var string|null $success */
/** @var ErrorList|null $error */
/** @var string $submitText */
/** @var int $displayCaptcha */
/** @var null|int $projectId */

$user = new User();

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var CaptchaInterface $captcha */
$captcha = $app->make(CaptchaInterface::class);
/** @var Token $token */
$token = $app->make(Token::class);

?>

<div class="create-ticket-form">
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

    <form action="<?php echo $controller->getActionURL("create_ticket"); ?>" method="post"
          enctype="multipart/form-data">
        <?php echo $token->output("ticket_create"); ?>

        <?php if (count($projectList) > 1) { ?>
            <div class="form-group">
                <?php echo $form->label('projectId', t("Project")); ?>
                <?php echo $form->select('projectId', $projectList, $projectId); ?>
            </div>
        <?php } else { ?>
            <?php echo $form->hidden('projectId', key($projectList)); ?>
        <?php } ?>

        <?php if (!$user->isRegistered()) { ?>
            <div class="form-group">
                <?php echo $form->label('email', t("E-Mail")); ?>
                <?php echo $form->email('email'); ?>
            </div>
        <?php } ?>

        <div class="form-group">
            <?php echo $form->label('title', t("Title")); ?>
            <?php echo $form->text('title'); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('content', t("Content")); ?>
            <?php echo $editor->outputStandardEditor('content'); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('ticketType', t("Type")); ?>
            <?php echo $form->select('ticketType', $ticketTypes); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('ticketPriority', t("Priority")); ?>
            <?php echo $form->select('ticketPriority', $ticketPriorities); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('ticketAttachment', t("Attachment")); ?>
            <?php echo $form->file('ticketAttachment[]', ["multiple" => "multiple", "id" => "ticketAttachment"]); ?>
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
            <?php echo $submitText; ?>
        </button>
    </form>
</div>