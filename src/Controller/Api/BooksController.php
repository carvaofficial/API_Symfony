<?php

namespace App\Controller\Api;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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
    public function postAction(EntityManagerInterface $em, Request $request, FileUploader $fileUploader)
    {
        $bookDTO = new BookDTO();

        $form = $this->createForm(BookFormType::class, $bookDTO);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid()) {
            $book = new Book();
            $book->setTitle($bookDTO->title);
            if ($bookDTO->base64Image) {
                $filename = '/books/' . $fileUploader->uploadBase64File($bookDTO->base64Image);
                $book->setImage($filename);
            }

            $em->persist($book);
            $em->flush();

            return $book;
        }

        return $form;
    }

    /**
     *@Rest\Post(path="/books/{id}", requirements={"id"="\d+"})
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function editAction(int $id, EntityManagerInterface $em, BookRepository $bookRepository, CategoryRepository $categoryRepository, Request $request, FileUploader $fileUploader)
    {
        $book = $bookRepository->find($id);
        if (!$book) throw $this->createNotFoundException('Book not found');

        $bookDTO = BookDTO::createFromBook($book);

        $originalCategories = new ArrayCollection();

        foreach ($book->getCategories() as $category) {
            $categoryDTO = CategoryDTO::createFromBook($category);
            $bookDTO->categories[] = $categoryDTO;
            $originalCategories->add($categoryDTO);
        }

        $form = $this->createForm(BookFormType::class, $bookDTO);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }

        if ($form->isValid()) {
            // Remove categories
            foreach ($originalCategories as $originalCategoryDTO) {
                if (!in_array($originalCategoryDTO, $bookDTO->categories)) {
                    $category = $categoryRepository->find($originalCategoryDTO->id);
                    $book->removeCategory($category);
                }
            }

            // Add categories
            foreach ($bookDTO->categories as $newCategoryDTO) {
                if (!$originalCategories->contains($newCategoryDTO)) {
                    $category = $categoryRepository->find($newCategoryDTO->id ?? 0);
                    if (!$category) {
                        $category = new Category();
                        $category->setName($newCategoryDTO->name);
                        $em->persist($category);
                    }

                    $book->addCategory($category);
                }
            }

            $book->setTitle($bookDTO->title);
            if ($bookDTO->base64Image) {
                $filename = $fileUploader->uploadBase64File($bookDTO->base64Image);
                $book->setImage($filename);
            }

            $em->persist($book);
            $em->flush();
            $em->refresh($book);

            return $book;
        }

        return $form;
    }
}
