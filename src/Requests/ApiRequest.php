<?php

declare(strict_types=1);

namespace Digikala\Requests;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiRequest
 * @package Digikala\Request
 */
class ApiRequest extends ParameterBag
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * ApiRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}