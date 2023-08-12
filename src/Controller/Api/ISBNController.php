<?php

namespace App\Controller\Api;

use App\Service\ISBN\GetBookByISBN;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ISBNController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/ISBN")
     *@Rest\View(serializerGroups={"book_isbn"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getAction(GetBookByISBN $getBookByISBN, Request $request): View
    {
        $isbn = $request->get('isbn');
        if (is_null($isbn)) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        $json = ($getBookByISBN)($isbn);

        return View::create($json);
    }
}
