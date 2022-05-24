<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Form\Model\BookDTO;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
    public function postAction(EntityManagerInterface $em, Request $request, FilesystemOperator $defaultStorage)
    {
        $bookDTO = new BookDTO();

        $form = $this->createForm(BookFormType::class, $bookDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $extension = explode('/', mime_content_type($bookDTO->base64Image))[1];
            $data = explode('.', $bookDTO->base64Image);
            $filename = sprintf('/books/%s.%s', uniqid('book_', true), $extension);
            $defaultStorage->write($filename, base64_decode($data[0]));

            $book = new Book();
            $book
                ->setTitle($bookDTO->title)
                ->setImage($filename);

            $em->persist($book);
            $em->flush();

            return $book;
        }

        return $form;
    }
}
