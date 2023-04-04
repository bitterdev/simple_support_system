<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Utility\Service\Identifier;

/** @var $packageHandle string */
$app = Application::getFacadeApplication();

/** @var Identifier $idHelper */
$idHelper = $app->make(Identifier::class);
$helpId = "ccm-help" . $idHelper->getString();

?>

<div class="alert alert-info">
    <?php echo t(
        "If you need support please click %s.",
        sprintf(
            "<a href=\"javascript:void(0);\" id=\"%s\">%s</a>",
            $helpId,
            t("here")
        )
    ); ?>
</div>

<script>
    (function ($) {
        $("#<?php echo $helpId; ?>").click(function () {
            jQuery.fn.dialog.open({
                href: "<?php echo (string)Url::to("/ccm/system/dialogs/simple_support_system/create_ticket"); ?>",
                modal: true,
                width: 500,
                title: "<?php echo h(t("Support"));?>",
                height: '80%'
            });
        });
    })(jQuery);
</script>
