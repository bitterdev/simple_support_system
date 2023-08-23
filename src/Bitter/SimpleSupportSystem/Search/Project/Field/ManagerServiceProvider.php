<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Bitter\SimpleSupportSystem\Search\Project\Field;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class ManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("manager/search_field/project", function ($app) {
            return $app->make('Bitter\SimpleSupportSystem\Search\Project\Field\Manager');
        });
    }
}
