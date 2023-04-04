<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Marketplace\Marketplace;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Package\Package;

/** @var $packageHandle string */
$app = Application::getFacadeApplication();
/** @var $packageService PackageService */
$packageService = $app->make(PackageService::class);
/** @var $pkg Package */
$pkg = $packageService->getByHandle($packageHandle);
/** @var $config Repository */
$config = $app->make(Repository::class);
?>

<?php if (is_object($pkg) && !$pkg->getConfig()->get('license_check.hide')): ?>

    <?php if (!Marketplace::getInstance()->isConnected()): ?>
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert" aria-label="close" onclick="hideLicenseCheck();">&times;</a>

        <?php echo t("You have not connected your site to the Marketplace. To benefit from updates, connect your site to the Marketplace and assign the purchased licenses."); ?>
        </div>
    <?php endif; ?>

    <script>
        function hideLicenseCheck() {
            $.ajax("<?php echo h((string)Url::to("/bitter/simple_support_system/hide_license_check")); ?>");
        };
    </script>
<?php endif; ?>
