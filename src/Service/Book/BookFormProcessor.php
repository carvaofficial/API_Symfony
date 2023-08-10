<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Entity\Book\Score;
use App\Form\Model\BookDTO;
use App\Form\Model\CategoryDTO;
use App\Form\Type\BookFormType;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Service\Book\GetBook;
use App\Service\Category\CreateCategory;
use App\Service\Category\GetCategory;
use App\Service\FileUploader;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookFormProcessor
{
    private BookRepository $bookRepository;

    private GetBook $getBook;

    private CategoryRepository $categoryRepository;

    private GetCategory $getCategory;

    private CreateCategory $createCategory;

    private FileUploader $fileUploader;

    private FormFactoryInterface $ffi;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        BookRepository $bookRepository,
        GetBook $getBook,
        CategoryRepository $categoryRepository,
        GetCategory $getCategory,
        CreateCategory $createCategory,
        FileUploader $fileUploader,
        FormFactoryInterface $ffi,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->bookRepository = $bookRepository;
        $this->getBook = $getBook;
        $this->categoryRepository = $categoryRepository;
        $this->getCategory = $getCategory;
        $this->createCategory = $createCategory;
        $this->fileUploader = $fileUploader;
        $this->ffi = $ffi;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(Request $request, ?string $bookId = null): array
    {
        $book = null;
        $bookDTO = null;

        if ($bookId === null) {
            $book = Book::create();
            $bookDTO = BookDTO::createEmpty();
        } else {
            $book = ($this->getBook)($bookId);
            $bookDTO = BookDTO::createFromBook($book);

            foreach ($book->getCategories() as $category) {
                $bookDTO->categories[] = CategoryDTO::createFromCategory($category);
            }
        }

        $form = $this->ffi->create(BookFormType::class, $bookDTO);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            return [null, 'Form is not submitted'];
        }
        if (!$form->isValid()) {
            return [null, $form];
        }

        $categories = [];

        foreach ($bookDTO->getCategories() as $newCategoryDTO) {
            $category = ($newCategoryDTO->getId() !== null) ? ($this->getCategory)($newCategoryDTO->getId()) : null;
            if (!$category) {
                $category = ($this->createCategory)($newCategoryDTO->getName());
            }
            $categories[] = $category;
        }

        $filename = ($bookDTO->getBase64Image()) ? $this->fileUploader->uploadBase64File($bookDTO->base64Image) : null;

        $book->update(
            $bookDTO->getTitle(),
            $filename,
            $bookDTO->getDescription(),
            Score::create($bookDTO->getScore()),
            $categories,
            []
        );
        $this->bookRepository->save($book);

        foreach ($book->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return [$book, null];
    }
}
