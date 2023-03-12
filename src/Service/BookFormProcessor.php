<?php

namespace App\Service;

use App\Entity\Book;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class BookFormProcessor
{
    private BookRepository $bookRepository;
    private CategoryManager $categoryManager;
    private FileUploader $fileUploader;
    private FormFactoryInterface $ffi;

    public function __construct(BookRepository $bookRepository, CategoryManager $categoryManager, FileUploader $fileUploader, FormFactoryInterface $ffi)
    {
        $this->bookRepository = $bookRepository;
        $this->categoryManager = $categoryManager;
        $this->fileUploader = $fileUploader;
        $this->ffi = $ffi;
    }

    public function __invoke(Book $book, Request $request): array
    {
        $bookDTO = BookDTO::createFromBook($book);

        /**
         * @var CategoryDTO[]|ArrayCollection $originalCategories
         */
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
                    $category = $this->categoryManager->find($originalCategoryDTO->getId());
                    $book->removeCategory($category);
                }
            }

            // Add categories
            foreach ($bookDTO->getCategories() as $newCategoryDTO) {
                if (!$originalCategories->contains($newCategoryDTO)) {
                    $category = ($newCategoryDTO->getId() !== null) ? $this->categoryManager->find($newCategoryDTO->getId()) : null;
                    if (!$category) {
                        $category = $this->categoryManager->create();
                        $category->setName($newCategoryDTO->getName());
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

            $this->bookRepository->save($book);

            return [$book, null];
        }

        return [null, $form];
    }
}
