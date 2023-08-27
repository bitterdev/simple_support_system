<?php

/**
 * @project:   Bitter Theme
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter (www.bitter.de)
 * @version    X.X.X
 */

namespace Concrete\Package\SimpleSupportSystem\Controller\Dialog\Support;

use Concrete\Controller\Backend\UserInterface;
use Concrete\Core\Application\EditResponse;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\ResponseFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/** @noinspection PhpUnused */

class CreateTicket extends UserInterface
{
    protected $viewPath = '/dialogs/support/create_ticket';

    protected function canAccess()
    {
        return true;
    }

    public function submit()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        $editResponse = new EditResponse();
        $errorList = new ErrorList();

        if ($this->request->getMethod() === 'POST') {
            $client = new Client();

            try {
                $response = $client->request('POST', 'https://www.bitter.de/index.php/api/v1/ticket/create', [
                    'form_params' => $this->request->request->all()
                ]);

                $rawData = $response->getBody()->getContents();

                /** @noinspection PhpComposerExtensionStubsInspection */
                $json = json_decode($rawData, true);

                if (isset($json["error"])) {
                    foreach ($json["errors"] as $errorMessage) {
                        $errorList->add($errorMessage);
                    }
                } else {
                    $editResponse->setTitle(t("Ticket Created"));
                    $editResponse->setMessage(t("Your ticket has been successfully created."));
                }
            } catch (GuzzleException $e) {
                $errorList->add($e);
            }
        }

        $editResponse->setError($errorList);

        return $responseFactory->json($editResponse);
    }

    public function view()
    {
        //
    }
}