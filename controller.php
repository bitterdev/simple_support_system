<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem;

use Bitter\SimpleSupportSystem\Provider\ServiceProvider;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Package\Package;
use Concrete\Core\Database\EntityManager\Provider\ProviderAggregateInterface;
use Concrete\Core\Database\EntityManager\Provider\StandardPackageProvider;
use Concrete\Core\Page\Page;
use Concrete\Core\Site\Service;
use Exception;

class Controller extends Package implements ProviderAggregateInterface
{
    protected $pkgHandle = 'simple_support_system';
    protected $pkgVersion = '1.6.0';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/SimpleSupportSystem' => 'Bitter\SimpleSupportSystem',
    ];

    public function getPackageDescription()
    {
        return t('Feature-rich self-hosted support system that fully complies with GDPR and doesn\'t require any 3rd party services.');
    }

    public function getPackageName()
    {
        return t('Simple Support System');
    }

    public function getEntityManagerProvider()
    {
        return new StandardPackageProvider($this->app, $this, [
            'src/Bitter/SimpleSupportSystem/Entity' => 'Bitter\SimpleSupportSystem\Entity'
        ]);
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    /**
     * @param bool $testForAlreadyInstalled
     * @return ErrorList|true|void
     * @throws Exception
     */
    public function testForInstall($testForAlreadyInstalled = true)
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        /*
        if (!$config->get('concrete.api.enabled')) {
            throw new Exception(t("You need to enable the API feature first to use this add-on."));
        }
        */

        if (!$config->get('concrete.user.registration.enabled')) {
            throw new Exception(t("You need to enable public registration first to use this add-on."));
        }
    }

    public function install()
    {
        parent::install();
        $this->installContentFile('install.xml');
        /** @var Service $siteService */
        $siteService = $this->app->make(Service::class);
        $site = $siteService->getSite();
        $config = $site->getConfigRepository();
        $config->save("simple_support_system.ticket_detail_page", Page::getByPath("/support/project/tickets")->getCollectionID());
    }

}