<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleSupportSystem\Entity\Project;
use Bitter\SimpleSupportSystem\Entity\Ticket;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var int $ticketDetailPageId */
/** @var int $createTicketPageId */
/** @var Ticket[] $tickets */
/** @var Project|null $project */
?>

<?php if ($project instanceof Project) { ?>
    <div class="header-actions">
        <a href="<?php echo (string)Url::to(Page::getByID($createTicketPageId), "filter_by_project", $project->getProjectId()); ?>"
           class="btn btn-primary pull-right">
            <?php echo t("Create Ticket"); ?>
        </a>
    </div>
<?php } ?>

<div class="clearfix"></div>

<div class="ticket-list">
    <?php if (count($tickets) > 0) { ?>
        <table class="table">
            <thead>
            <tr>
                <th>
                    <?php echo t("Title"); ?>
                </th>
                <th>
                    <?php echo t("Type"); ?>
                </th>
                <th>
                    <?php echo t("Priority"); ?>
                </th>
                <th>
                    <?php echo t("Status"); ?>
                </th>
                <th>
                    <?php echo t("Created"); ?>
                </th>
                <th>
                    <?php echo t("Updated"); ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($tickets as $ticket) { ?>
                <tr>
                    <td>
                        <a href="<?php echo (string)Url::to(Page::getByID($ticketDetailPageId), "display_ticket", $ticket->getTicketId()); ?>">
                            #<?php echo $ticket->getTicketId(); ?>: <?php echo $ticket->getTitle(); ?>
                        </a>
                    </td>

                    <td>
                        <?php echo $ticket->getTicketTypeDisplayValue(); ?>
                    </td>

                    <td>
                        <?php echo $ticket->getTicketPriorityDisplayValue(); ?>
                    </td>

                    <td>
                        <?php echo $ticket->getTicketStateDisplayValue(); ?>
                    </td>

                    <td>
                        <?php echo $ticket->getCreatedAtDisplayValue(); ?>
                    </td>

                    <td>
                        <?php echo $ticket->getUpdatedAtDisplayValue(); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>
            <?php echo t("There are no tickets available."); ?>
        </p>
    <?php } ?>
</div>