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

/** @var int $ticketDetailPageId */
/** @var int $createTicketPageId */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

\Concrete\Core\View\View::element("dashboard/help_blocktypes", [], "simple_support_system");

/** @noinspection PhpUnhandledExceptionInspection */
\Concrete\Core\View\View::element("dashboard/did_you_know", [], "simple_support_system");
?>

<div class="form-group">
    <?php echo $form->label('ticketDetailPageId', t("Ticket Detail Page")); ?>
    <?php echo $pageSelector->selectPage('ticketDetailPageId', $ticketDetailPageId); ?>
</div>

<div class="form-group">
    <?php echo $form->label('createTicketPageId', t("Create Ticket Page")); ?>
    <?php echo $pageSelector->selectPage('createTicketPageId', $createTicketPageId); ?>
</div>
