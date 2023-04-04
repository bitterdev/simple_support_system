<?php

/**
 * @project:   Simple Support System
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\SinglePage\Dashboard\SimpleSupportSystem;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Page\Page;

class Settings extends DashboardPageController
{
    /** @var Repository */
    protected $config;
    /** @var Validation */
    protected $formValidator;

    public function on_start()
    {
        parent::on_start();

        $this->config = $this->app->make(Repository::class);
        $this->formValidator = $this->app->make(Validation::class);
    }

    public function view()
    {
        if ($this->request->getMethod() === "POST") {
            $this->formValidator->setData($this->request->request->all());
            $this->formValidator->addRequiredToken("update_settings");
            $this->formValidator->addRequiredEmail("notificationMailAddress", t("You need to enter a valid mail address."));
            $this->formValidator->addRequired("ticketDetailPage", t("You need to select the detail page."));

            if ($this->formValidator->test()) {
                $page = Page::getByID($this->request->request->getInt('ticketDetailPage'));

                if ($page instanceof Page && !$page->isError()) {
                    $this->config->save('simple_support_system.notification_mail_address', $this->request->request->get('notificationMailAddress'));
                    $this->config->save('simple_support_system.ticket_detail_page', $this->request->request->getInt('ticketDetailPage'));
                    $this->config->save('simple_support_system.enable_moderation', $this->request->request->has('enableModeration'));

                    $this->set('success', t("The settings has been successfully updated"));
                } else {
                    $this->error->add(t("You need to select a valid detail page."));
                }
            } else {
                $this->error = $this->formValidator->getError();
            }
        }

        $this->set('notificationMailAddress', $this->config->get('simple_support_system.notification_mail_address'));
        $this->set('ticketDetailPage', $this->config->get('simple_support_system.ticket_detail_page'));
        $this->set('enableModeration', (int)$this->config->get('simple_support_system.enable_moderation') === 1);
    }
}
