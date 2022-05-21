<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    // private $logger;

    // public function __construct(LoggerInterface $logger)
    // {
    //     $this->logger = $logger;
    // }


    /**
     * @Route("/books", name="books_list")
     */
    public function list(Request $request, LoggerInterface $logger, BookRepository $bookRepository)
    {
        // $this->logger->info('List action called (service as property of class)');
        $logger->info('List action called (service as argument)');

        $title = $request->get('title', 'Alegría');

        $books = $bookRepository->findAll();

        $booksAsArray = [];
        foreach ($books as $book) {
            $booksAsArray[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'image' => $book->getImage(),
            ];
        }

        $response = new JsonResponse();
        $response->setData($booksAsArray);

        return $response;
    }

    /**
     * @Route("/book/create", name="create_book")
     */
    public function CreateBook(Request $request, EntityManagerInterface $em)
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

        $response->setData([
            'success' => true,
            'data' => [
                [
                    'id' => $book->getId(),
                    'title' => $book->getTitle()
                ],
            ]
        ]);

        return $response;
    }
}
