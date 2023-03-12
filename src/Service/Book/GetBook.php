<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Model\Exception\Book\BookNotFound;
use App\Repository\BookRepository;
use Ramsey\Uuid\Uuid;

class GetBook
{
    private BookRepository $bookRepositroy;

    public function __construct(BookRepository $bookRepositroy)
    {
        $this->bookRepositroy = $bookRepositroy;
    }

    public function __invoke(string $id): ?Book
    {
        $book = $this->bookRepositroy->find(Uuid::fromString($id));

        if (!$book) {
            BookNotFound::throwException();
        }

        return $book;
    }
}
