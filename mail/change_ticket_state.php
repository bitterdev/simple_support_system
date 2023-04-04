<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\SimpleSupportSystem\Entity\Ticket;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var Ticket $ticket */
/** @var Page $ticketDetailPage */

$subject = t(
    '[Change Ticket State] [%s] Ticket %s: %s',
    $ticket->getProject()->getProjectName(),
    $ticket->getTicketId(),
    $ticket->getTitle()
);

$bodyHTML = "<p>" . t("The ticket state has been changed to: %s", $ticket->getTicketStateDisplayValue()) . "</p>";
$bodyHTML .= "<br>";
$bodyHTML .= t("If you can't click the issue link, please copy the link manually and paste it to the address bar of your browser.") . "<br>";
/** @noinspection PhpFormatFunctionParametersMismatchInspection */
$bodyHTML .=
    sprintf(
        "<a href=\"%s\" target=\"_blank\">%s</a>",
        (string)Url::to($ticketDetailPage, "display_ticket", $ticket->getTicketId()),
        (string)Url::to($ticketDetailPage, "display_ticket", $ticket->getTicketId())
    );

$body = strip_tags(str_replace(["<br>", "<br/>"], "\r\n", $bodyHTML));