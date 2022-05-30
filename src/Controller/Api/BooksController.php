<?php

namespace App\Controller\Api;

use App\Service\BookFormProcessor;
use App\Service\BookManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BooksController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/books")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getAction(BookManager $bookManager)
    {
        return $bookManager->getRepository()->findAll();
    }

    /**
     *@Rest\Post(path="/books")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    // public function postAction(Request $request, EntityManagerInterface $em)
    // {
    //     $response = new JsonResponse();
    //     $title = $request->get('title', null);

    //     if (empty($title)) {
    //         $response->setData([
    //             'success' => false,
    //             'error' => 'Tittle cannot be empty',
    //             'data' => null
    //         ]);

    //         return $response;
    //     }

    //     $book = new Book();
    //     $book->setTitle($title);
    //     $em->persist($book);
    //     $em->flush();

    //     return $book;
    // }
    public function postAction(BookManager $bookManager, BookFormProcessor $bfp, Request $request)
    {
        $book = $bookManager->create();
        [$book, $error] = ($bfp)($book, $request);

        return View::create($book ?? $error, $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Get(path="/books/{id}", requirements={"id"="\d+"})
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getSingleAction(int $id, BookFormProcessor $bfp, BookManager $bookManager)
    {
        $book = $bookManager->find($id);
        return !$book ? View::create('Book not found', Response::HTTP_BAD_REQUEST) : $book;
    }

    /**
     *@Rest\Post(path="/books/{id}", requirements={"id"="\d+"})
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function editAction(int $id, BookFormProcessor $bfp, BookManager $bookManager, Request $request)
    {
        $book = $bookManager->find($id);
        if (!$book) return View::create('Book not found', Response::HTTP_BAD_REQUEST);

        [$book, $error] = ($bfp)($book, $request);

        return View::create($book ?? $error, $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Delete(path="/books/{id}", requirements={"id"="\d+"})
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function deleteAction(int $id, BookManager $bookManager, Request $request)
    {
        $book = $bookManager->find($id);
        if (!$book) return View::create('Book not found', Response::HTTP_BAD_REQUEST);

        $bookManager->delete($book);

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
