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
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var int $displayCaptcha */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'simple_support_system');

?>

<div class="checkbox">
    <label for="displayCaptcha">
        <?php echo $form->checkbox("displayCaptcha", 1, (int)$displayCaptcha === 1); ?>

        <?php echo t("Display Captcha"); ?>
    </label>
</div>