<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Routing\Router;

/**
 * @var Router $router
 * Base path: /ccm/system/search/project
 * Namespace: Concrete\Package\SimpleSupportSystem\Controller\Search
 */

$router->all('/basic', 'Project::searchBasic');
$router->all('/current', 'Project::searchCurrent');
$router->all('/preset/{presetID}', 'Project::searchPreset');
$router->all('/clear', 'Project::clearSearch');
