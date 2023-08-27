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
use Concrete\Core\View\View;

/** @var string $thankYouMessage */
/** @var string $submitText */
/** @var int $displayCaptcha */
/** @var int $redirectCID */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

View::element("dashboard/help_blocktypes", [], "simple_support_system");
?>

<div class="form-group">
    <?php echo $form->label('submitText', t('Submit Text')); ?>
    <?php echo $form->text('submitText', $submitText); ?>
</div>

<div class="form-group">
    <?php echo $form->label('thankYouMessage', t("Message to display when completed")); ?>
    <?php echo $form->textarea('thankYouMessage', $thankYouMessage, ['rows' => 3]); ?>
</div>

<div class="form-group">
    <?php echo $form->label('redirectCID', t("Redirect to another page after form submission?")); ?>
    <?php echo $pageSelector->selectPage('redirectCID', $redirectCID); ?>
</div>

<p class="help-block">
    <?php echo t("This page needs to contain the detail block."); ?>
</p>

<div class="checkbox">
    <label for="displayCaptcha">
        <?php echo $form->checkbox("displayCaptcha", 1, (int)$displayCaptcha === 1); ?>

        <?php echo t("Display Captcha"); ?>
    </label>
</div>
