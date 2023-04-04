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
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;

/** @var int $createTicketPageId */
/** @var int $ticketListPageId */
/** @var Project[] $projects */
?>

<div class="header-actions">
    <a href="<?php echo (string)Url::to(Page::getByID($createTicketPageId)); ?>"
       class="btn btn-primary pull-right">
        <?php echo t("Create Ticket"); ?>
    </a>
</div>

<div class="clearfix"></div>

<div class="project-list">
    <?php if (count($projects) > 0) { ?>
        <table class="table">
            <thead>
            <tr>
                <th>
                    <?php echo t("Title"); ?>
                </th>

                <th>
                    <?php echo t("Total Tickets"); ?>
                </th>
            </tr>
            <tbody>
            <?php foreach ($projects as $project) { ?>
                <tr>
                    <td>
                        <a href="<?php echo (string)Url::to(Page::getByID($ticketListPageId), "filter_by_project", $project->getProjectId()); ?>">
                            <?php echo $project->getProjectName(); ?>
                        </a>
                    </td>

                    <td>
                        <?php echo count($project->getTickets()); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>
            <?php echo t("There are no projects available."); ?>
        </p>
    <?php } ?>
</div>
