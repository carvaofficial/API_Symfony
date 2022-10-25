<?php

namespace App\Service;

use App\Entity\Book;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BookFormProcessor
{
    private $em;
    private $bookManager;
    private $categoryManager;
    private $fileUploader;
    private $ffi;

    public function __construct(EntityManagerInterface $em, BookManager $bookManager, CategoryManager $categoryManager, FileUploader $fileUploader, FormFactoryInterface $ffi)
    {
        $this->em = $em;
        $this->bookManager = $bookManager;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->ffi = $ffi;
    }

    public function __invoke(Book $book, Request $request): array
    {
        $bookDTO = BookDTO::createFromBook($book);

        $originalCategories = new ArrayCollection();

        foreach ($book->getCategories() as $category) {
            $categoryDTO = CategoryDTO::createFromBook($category);
            $bookDTO->categories[] = $categoryDTO;
            $originalCategories->add($categoryDTO);
        }

        $form = $this->ffi->create(BookFormType::class, $bookDTO);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }

        if ($form->isValid()) {
            // Remove categories
            foreach ($originalCategories as $originalCategoryDTO) {
                if (!\in_array($originalCategoryDTO, $bookDTO->categories)) {
                    $category = $this->categoryManager->find($originalCategoryDTO->id);
                    $book->removeCategory($category);
                }
            }

            // Add categories
            foreach ($bookDTO->categories as $newCategoryDTO) {
                if (!$originalCategories->contains($newCategoryDTO)) {
                    $category = $this->categoryManager->find($newCategoryDTO->id ?? 0);
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDTO->name);
                        $this->categoryManager->persist($category);
                    }

                    $book->addCategory($category);
                }
            }

            $book->setTitle($bookDTO->title);
            if ($bookDTO->base64Image) {
                $filename = $this->fileUploader->uploadBase64File($bookDTO->base64Image);
                $book->setImage($filename);
            }

            $this->bookManager->save($book);
            $this->bookManager->reload($book);

            return [$book, null];
        }

        return [null, $form];
    }
}
