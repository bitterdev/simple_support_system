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
 * Base path: /ccm/system/dialogs/project
 * Namespace: Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project
 */

$router->all('/advanced_search', 'AdvancedSearch::view');
$router->all('/advanced_search/add_field', 'AdvancedSearch::addField');
$router->all('/advanced_search/submit', 'AdvancedSearch::submit');
$router->all('/advanced_search/save_preset', 'AdvancedSearch::savePreset');
$router->all('/advanced_search/preset/edit', 'Preset\Edit::view');
$router->all('/advanced_search/preset/edit/edit_search_preset', 'Preset\Edit::edit_search_preset');
$router->all('/advanced_search/preset/delete', 'Preset\Delete::view');
$router->all('/advanced_search/preset/delete/remove_search_preset', 'Preset\Delete::remove_search_preset');

$router->all('/ccm/system/search/project/basic', '\Concrete\Package\SimpleSupportSystem\Controller\Search\Project::searchBasic');
$router->all('/ccm/system/search/project/current', '\Concrete\Package\SimpleSupportSystem\Controller\Search\Project::searchCurrent');
$router->all('/ccm/system/search/project/preset/{presetID}', '\Concrete\Package\SimpleSupportSystem\Controller\Search\Project::searchPreset');
$router->all('/ccm/system/search/project/clear', '\Concrete\Package\SimpleSupportSystem\Controller\Search\Project::clearSearch');
