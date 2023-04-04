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
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Concrete\Package\SimpleSupportSystem\Controller\Dialog\Support\CreateTicket;

$ticketTypes = [
    "bug" => t("Bug"),
    "enhancement" => t("Enhancement"),
    "proposal" => t("Proposal"),
    "task" => t("Task")
];

$ticketPriorities = [
    "trivial" => t("Trivial"),
    "minor" => t("Minor"),
    "major" => t("Major"),
    "critical" => t("Critical"),
    "blocker" => t("Blocker")
];

/** @var CreateTicket $controller */
/** @var null|int $projectId */

$user = new User();

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);
/** @var CaptchaInterface $captcha */
$captcha = $app->make(CaptchaInterface::class);

?>

<form action="<?php echo (string)Url::to("/ccm/system/dialogs/simple_support_system/create_ticket/submit"); ?>"
      data-dialog-form="create-ticket"
      method="post"
      enctype="multipart/form-data">

    <?php echo $form->hidden('projectHandle', "simple_support_system"); ?>

    <div class="form-group">
        <?php echo $form->label('email', t("E-Mail")); ?>
        <?php echo $form->email('email'); ?>
    </div>

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

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary pull-right">
            <?php echo t('Create Ticket') ?>
        </button>
    </div>
</form>