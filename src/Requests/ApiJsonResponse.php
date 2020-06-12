<?php

declare(strict_types=1);

namespace Digikala\Requests;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiJsonResponse
 * @package Digikala\Requests
 */
class ApiJsonResponse
{
    /**
     * @param array $errors
     * @return JsonResponse
     */
    public function error(array $errors)
    {
        return (new JsonResponse(
            [
                'Data' => [],
                'Message' => $errors
            ],
            Response::HTTP_BAD_REQUEST
        ))->send();
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function success($data = [])
    {
        return (new JsonResponse(
            [
                'Data' => $data,
                'Message' => ''
            ],
            Response::HTTP_OK
        ))->send();
    }
}