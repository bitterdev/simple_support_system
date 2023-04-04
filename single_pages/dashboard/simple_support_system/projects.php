<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access denied');

use Bitter\SimpleSupportSystem\Search\Project\Result\Result;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;

/** @var Result|null $result */

/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/help', null, 'simple_support_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/reminder', ["packageHandle" => "simple_support_system", "rateUrl" => "https://www.concrete5.org/marketplace/addons/simple-support-system/reviews"], 'simple_support_system');
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/license_check', ["packageHandle" => "simple_support_system"], 'simple_support_system');

?>

<?php if (!is_object($result)): ?>
    <div class="alert alert-warning">
        <?php echo t('Currently there are no projects available.'); ?>
    </div>
<?php else: ?>
    <script type="text/template" data-template="search-results-table-body">
        <% _.each(items, function (item) {%>
        <tr data-launch-search-menu="<%=item.id%>">
            <td class="ccm-search-results-icon">
                <%=item.resultsThumbnailImg%>
            </td>
            <% for (i = 0; i < item.columns.length; i++) {
            var column = item.columns[i]; %>
            <% if (i == 0) { %>
            <td class="ccm-search-results-name"><%-column.value%></td>
            <% } else { %>
            <td><%-column.value%></td>
            <% } %>
            <% } %>
        </tr>
        <% }); %>
    </script>

    <div data-search-element="wrapper"></div>

    <div data-search-element="results">
        <div class="table-responsive">
            <table class="ccm-search-results-table ccm-search-results-table-icon">
                <thead></thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="ccm-search-results-pagination"></div>
    </div>

    <script type="text/template" data-template="search-results-pagination">
        <%=paginationTemplate%>
    </script>
    <script type="text/template" data-template="search-results-menu">
        <div class="popover fade" data-search-menu="<%=item.id%>">
            <div class="arrow"></div>
            <div class="popover-inner">
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?php echo Url::to("/dashboard/simple_support_system/projects/edit"); ?>/<%=item.id%>">
                            <?php echo t("Edit"); ?>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo Url::to("/dashboard/simple_support_system/projects/remove"); ?>/<%=item.id%>">
                            <?php echo t("Remove"); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </script>


    <script type="text/template" data-template="search-results-table-head">
        <tr>
            <th>
                <div class="dropdown">
                    <button class="btn btn-menu-launcher" disabled data-toggle="dropdown"><i
                            class="fa fa-chevron-down"></i></button>
                </div>
            </th>
            <%
            for (i = 0; i < columns.length; i++) {
            var column = columns[i];
            if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
            <% } else { %>
            <th><span><%-column.title%></span></th>
            <% } %>
            <% } %>
        </tr>
    </script>

    <script type="text/javascript">
        $(function () {
            $('#ccm-dashboard-content').concreteAjaxSearch(<?php echo json_encode(["result" => $result->getJSONObject()]) ?>);
        });
    </script>
<?php endif; ?>
<?php
/** @noinspection PhpUnhandledExceptionInspection */
View::element('/dashboard/did_you_know', ["packageHandle" => "simple_support_system"], 'simple_support_system');
