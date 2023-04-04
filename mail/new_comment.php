<?php /** @noinspection DuplicatedCode */

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die("Access Denied.");

use Bitter\SimpleSupportSystem\Entity\TicketComment;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var TicketComment $ticketComment */
/** @var Page $ticketDetailPage */

$subject = t(
    '[New Comments] [%s] Ticket %s: %s',
    $ticketComment->getTicket()->getProject()->getProjectName(),
    $ticketComment->getTicket()->getTicketId(),
    $ticketComment->getTicket()->getTitle()
);

$bodyHTML = $ticketComment->getContent();
$bodyHTML .= "<br>";
$bodyHTML .= t("If you can't click the issue link, please copy the link manually and paste it to the address bar of your browser.") . "<br>";
/** @noinspection PhpFormatFunctionParametersMismatchInspection */
$bodyHTML .= sprintf(
    "<a href=\"%s\" target=\"_blank\">%s</a>",
    (string)Url::to($ticketDetailPage, "display_ticket", $ticketComment->getTicket()->getTicketId()),
    (string)Url::to($ticketDetailPage, "display_ticket", $ticketComment->getTicket()->getTicketId())
);

$body = strip_tags(str_replace(["<br>", "<br/>"], "\r\n", $bodyHTML));