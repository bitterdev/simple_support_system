<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem;

use Bitter\SimpleSupportSystem\API\V1\Middleware\FractalNegotiatorMiddleware;
use Bitter\SimpleSupportSystem\API\V1\Projects;
use Bitter\SimpleSupportSystem\API\V1\Tickets;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setPrefix('/api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function ($groupRouter) {
                /** @var $groupRouter Router */
                $groupRouter->get('/project/list', [Projects::class, 'list']);
                $groupRouter->post('/ticket/create', [Tickets::class, 'create']);
            });

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleSupportSystem\Controller\Dialog\Project')
            ->setPrefix('/ccm/system/dialogs/project')
            ->routes('dialogs/project.php', 'simple_support_system');

        $router->buildGroup()->setNamespace('Concrete\Package\SimpleSupportSystem\Controller\Search')
            ->setPrefix('/ccm/system/search/project')
            ->routes('search/project.php', 'simple_support_system');

        $router
            ->buildGroup()
            ->setNamespace('Concrete\Package\SimpleSupportSystem\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/simple_support_system')
            ->routes('dialogs/support.php', 'simple_support_system');
    }
}