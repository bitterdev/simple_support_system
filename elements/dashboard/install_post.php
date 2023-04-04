<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');
?>

<!--suppress CssUnusedSymbol -->
<style type="text/css">
    .ui-dialog-title {
        visibility: hidden;
    }
</style>

<div class="row">
    <div class="col-xs-12">
        <h2>
            <?php echo t("Thank You for purchasing this add-on!"); ?>
        </h2>

        <p>
            <?php echo t("Follow me on Twitter and Facebook to get the latest news and notifications about updates, new add-ons/themes and special offers."); ?>
        </p>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fbitter.de%2F&width=114&layout=button_count&action=like&size=small&show_faces=false&share=false&height=21&appId=211616379422132" width="114" height="21" style="border:none;overflow:hidden" allowTransparency="true"></iframe>

        <a href="https://twitter.com/fabian_bitter?ref_src=twsrc%5Etfw" class="twitter-follow-button" data-show-count="false"><?php echo t("Follow @fabian_bitter"); ?></a><!--suppress JSUnresolvedLibraryURL -->
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    </div>
</div>
