<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BooksController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/books")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getActions(BookRepository $bookRepository)
    {
        return $bookRepository->findAll();
    }

    /**
     *@Rest\Post(path="/books")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function postActions(Request $request, EntityManagerInterface $em)
    {
        $response = new JsonResponse();
        $title = $request->get('title', null);

        if (empty($title)) {
            $response->setData([
                'success' => false,
                'error' => 'Tittle cannot be empty',
                'data' => null
            ]);

            return $response;
        }

        $book = new Book();
        $book->setTitle($title);
        $em->persist($book);
        $em->flush();

        return $book;
    }
}
