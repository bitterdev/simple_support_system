<?php

/**
 * @author     Fabian Bitter
 * @copyright  (C) 2016 Fabian Bitter (www.bitter.de)
 * @version    1.2.1
 */

defined('C5_EXECUTE') or die('Access Denied.');
?>

<script>
    (function ($) {
        var $dialog = $( ".ui-dialog-content" );
        var $helpButton = $('<button class="btn-help"><svg><use xlink:href="#icon-dialog-help" /></svg></button>');
        $helpButton.insertBefore($dialog.parent().find('.ui-dialog-titlebar-close'));

        $helpButton.click(function () {
            jQuery.fn.dialog.open({
                href: "<?php echo (string)\Concrete\Core\Support\Facade\Url::to("/ccm/system/dialogs/simple_support_system/create_ticket"); ?>",
                modal: true,
                width: 500,
                title: "<?php echo h(t("Support"));?>",
                height: '80%'
            });
        });
    })(jQuery);
</script>