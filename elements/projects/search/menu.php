<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Utility\Service\Url;

/** @var $itemsPerPage int */
/** @var $urlHelper Url */
/** @var $packageHandle string */

?>

<div class="row row-cols-auto g-0 align-items-center">
    <?php if (!empty($itemsPerPageOptions)): ?>
        <div class="col-auto">
            <div class="btn-group">
                <button
                    type="button"
                    class="btn btn-secondary p-2 dropdown-toggle"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">

                <span id="selected-option">
                    <?php echo $itemsPerPage; ?>
                </span>
                </button>

                <ul class="dropdown-menu">
                    <li class="dropdown-header">
                        <?php echo t('Items per page') ?>
                    </li>

                    <?php foreach ($itemsPerPageOptions as $itemsPerPageOption): ?>
                        <?php
                        $url = $urlHelper->setVariable([
                            'itemsPerPage' => $itemsPerPageOption
                        ]);
                        ?>

                        <li data-items-per-page="<?php echo $itemsPerPageOption; ?>">
                            <a class="dropdown-item <?php echo ($itemsPerPageOption === $itemsPerPage) ? 'active' : ''; ?>"
                               href="<?php echo h($url) ?>">
                                <?php echo $itemsPerPageOption; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>

    <ul class="ccm-dashboard-header-icons">
        <li>
            <a  class="ccm-hover-icon launch-tooltip" title="<?php echo h(t('Add Project')) ?>" href="<?php echo \Concrete\Core\Support\Facade\Url::to("/dashboard/simple_support_system/projects/add"); ?>">
                <i class="fas fa-plus" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
</div>