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
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;
use Concrete\Core\View\View;

/** @var $entry Project */
/** @var $form Form */
/** @var $token Token */

?>
<form action="#" method="post">
    <?php echo $token->output("save_project_entity"); ?>

    <div class="form-group">
        <?php echo $form->label(
            "projectName",
            t("Project Name"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <?php echo $form->text(
            "projectName",
            $entry->getProjectName(),
            [
                "class" => "form-control",
                "max-length" => "255",
            ]
        ); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label(
            "projectHandle",
            t("Project Handle"),
            [
                "class" => "control-label"
            ]
        ); ?>

        <?php echo $form->text(
            "projectHandle",
            $entry->getProjectHandle(),
            [
                "class" => "form-control",
                "max-length" => "255",
            ]
        ); ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?php echo Url::to("/dashboard/simple_support_system/projects"); ?>" class="btn btn-secondary">
                <i class="fa fa-chevron-left"></i> <?php echo t('Back'); ?>
            </a>

            <div class="float-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save" aria-hidden="true"></i> <?php echo t('Save'); ?>
                </button>
            </div>
        </div>
    </div>
</form>
