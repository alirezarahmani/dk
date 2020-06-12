<?php

namespace Digikala\Controller;

use Digikala\Lib\View\Render;
use Digikala\Repository\NonPersistence\NotificationInMemoryRepository;
use Digikala\Services\MemcachedService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SmsController
 * @package Digikala\Controller\Apiv1
 */
class ReportController
{

    public function indexAction()
    {
        $render =  new Render();
        return (new Response($render->html()))->send();
    }

}


