<?php

namespace Digikala\Controller\Apiv1;

use Assert\InvalidArgumentException;
use Boot\Start;
use Digikala\Requests\ApiJsonResponse;
use Digikala\Requests\ApiRequest;
use Digikala\Services\NotificationService;

/**
 * Class SmsController
 * @package Digikala\Controller\Apiv1
 */
class SmsController
{
    /**
     * @param ApiRequest $apiRequest
     * @param ApiJsonResponse $apiJsonResponse
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Assert\AssertionFailedException
     */
    public function indexAction(ApiRequest $apiRequest, ApiJsonResponse $apiJsonResponse)
    {
        $container = Start::getContainer();
        /** @var NotificationService $notificationService */
        $notificationService = $container->get(NotificationService::class);
        try {
            $notificationService->send($apiRequest->getRequest()->get('number', ''), $apiRequest->getRequest()->get('body', ''));
            return $apiJsonResponse->success();
        } catch (InvalidArgumentException $exception) {
            return $apiJsonResponse->error([$exception->getMessage()]);
        }
    }
}