<?php


namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends AbstractFOSRestController
{

    /**
     * Creates an Article resource
     * @Rest\Get("/test")
     * @return View
     */
    public function getTest(): View
    {

        $data = ['imie'=>'Mateusz'];
        // In case our POST was a success we need to return a 201 HTTP CREATED response
        return View::create($data, Response::HTTP_OK);
    }
}
