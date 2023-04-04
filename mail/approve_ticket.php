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
    '[Approve Ticket] [%s] Ticket %s: %s',
    $ticket->getProject()->getProjectName(),
    $ticket->getTicketId(),
    $ticket->getTitle()
);

$bodyHTML = $ticket->getContent();
$bodyHTML .= "<br>";
$bodyHTML .= t("Click the following link to approve the ticket if it's no spam.") . "<br>";
/** @noinspection PhpFormatFunctionParametersMismatchInspection */
$bodyHTML .= sprintf(
    "<a href=\"%s\" target=\"_blank\">%s</a>",
    (string)Url::to($ticketDetailPage, "approve_ticket", $ticket->getTicketId()),
    (string)Url::to($ticketDetailPage, "approve_ticket", $ticket->getTicketId())
);

$body = strip_tags(str_replace(["<br>", "<br/>"], "\r\n", $bodyHTML));