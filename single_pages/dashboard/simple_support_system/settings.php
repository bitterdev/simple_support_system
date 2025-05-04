<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var string $notificationMailAddress */
/** @var int $ticketDetailPage */
/** @var bool $enableModeration */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var Token $token */
$token = $app->make(Token::class);


?>

<div class="ccm-dashboard-header-buttons">
    <?php \Concrete\Core\View\View::element("dashboard/help", [], "simple_support_system"); ?>
</div>

<?php \Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_support_system"); ?>

<form action="#" method="post">
    <?php echo $token->output("update_settings"); ?>

    <div class="form-group">
        <?php echo $form->label('notificationMailAddress', t('Notification Mail Address')); ?>
        <?php echo $form->email('notificationMailAddress', $notificationMailAddress); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('ticketDetailPage', t("Ticket Detail Page")); ?>
        <?php echo $pageSelector->selectPage('ticketDetailPage', $ticketDetailPage); ?>
    </div>

    <div class="form-group">
        <label class=" checkbox-inline">
            <?php echo $form->checkbox('enableModeration', 1, $enableModeration); ?>

            <?php echo t("Enable Moderation"); ?>
        </label>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions ">
            <button type="submit" class="btn btn-primary float-end">
                <i class="fa fa-save"></i> <?php echo t("Save"); ?>
            </button>
        </div>
    </div>
</form>
