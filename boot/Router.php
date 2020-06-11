<?php

namespace Boot;

use Digikala\Requests\ApiJsonResponse;
use Digikala\Requests\ApiRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class Router
{
    //@todo: create a routerSubscriber and remove this
    //@todo: create a dependency resolver for controller arguments
    public static function routes()
    {
        try {
            /** @var Request $request */
            $request = Start::getContainer()->get(Request::class);
            $apiRequest = new ApiRequest($request);
            $apiResponse = new ApiJsonResponse();

            $context = new RequestContext('/');
            $context->fromRequest($request);
            $matcher = new UrlMatcher(self::initRoutes(), $context);
            if ($parameters = $matcher->match($request->getPathInfo())) {
                if (isset($parameters['id'])) {
                    return call_user_func(
                        [$parameters['_controller'], $parameters['_method']],
                        $parameters['id'],
                        $apiRequest,
                        $apiResponse
                    );
                }
                return call_user_func(
                    [$parameters['_controller'], $parameters['_method']],
                    $apiRequest,
                    $apiResponse
                );
            }
        } catch (ResourceNotFoundException | MethodNotAllowedException $e) {
            return (new JsonResponse(['sorry requested page not found'], 404))->send();
        }
    }

    private static function initRoutes()
    {
        $indexRoute = new Route(
            '/jobs',
            ['_controller' => 'Digikala\\Controller\\Apiv1\\SmsController', '_method' => 'indexAction'],
            [],
            [],
            '',
            [],
            'GET'
        );
        $routes = new RouteCollection();
        $routes->add('job_index', $indexRoute);
        return $routes;
    }
}