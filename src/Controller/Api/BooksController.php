<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\Book\DeleteBook;
use App\Service\Book\GetBook;
use App\Service\BookFormProcessor;
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
    public function getAction(BookRepository $bookRepository)
    {
        return $bookRepository->findAll();
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

    public function postAction(BookFormProcessor $bfp, Request $request)
    {
        $book = Book::create();
        [$book, $error] = ($bfp)($request);

        return View::create($book ?? $error, $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Get(path="/books/{id}")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getSingleAction(string $id, GetBook $getBook)
    {
        /**
         * Método para poder llamar al método __invoke de una clase y pasarle parámetros
         */
        $book = ($getBook)($id);
        return !$book ? View::create('Book not found', Response::HTTP_BAD_REQUEST) : $book;
    }

    /**
     *@Rest\Post(path="/books/{id}")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function editAction(string $id, BookFormProcessor $bfp, GetBook $getBook, Request $request)
    {
        $book = ($getBook)($id);
        if (!$book) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        [$book, $error] = ($bfp)($request, $id);

        return View::create($book ?? $error, $book ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Delete(path="/books/{id}")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function deleteAction(string $id, DeleteBook $deleteBook, Request $request)
    {
        try {
            ($deleteBook)($id);
        } catch (\Throwable $th) {
            return View::create('Book not found', Response::HTTP_BAD_REQUEST);
        }

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
