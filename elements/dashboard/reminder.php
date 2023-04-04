<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;

/** @var $packageHandle string */
/** @var $rateUrl string */
$app = Application::getFacadeApplication();
/** @var $packageService PackageService */
$packageService = $app->make(PackageService::class);
/** @var $pkg Package */
$pkg = $packageService->getByHandle($packageHandle);
?>

<?php if (date("Y") <= 2021 && is_object($pkg) && !$pkg->getConfig()->get('reminder.hide')): ?>
    <div class="alert alert-info alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="hideAlert();">&times;</a>

        <?php echo t(
            "Rate this add-on on concrete5.org and as a thank-you gift you can choose another add-on of mine up to $35. This offer may only be claimed once. Click %s to rate now.",
            sprintf(
                "<a href=\"" . $rateUrl . "\">%s</a>",
                t("here")
            )
        ); ?>
    </div>

    <script>
        function hideAlert() {
            $.ajax("<?php echo h((string)Url::to("/bitter/simple_support_system/hide_reminder")); ?>");
        };
    </script>
<?php endif; ?>