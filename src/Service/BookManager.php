<?php

namespace App\Service;

use App\Entity\Book;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

class BookManager
{
    private $em;
    private $bookRepository;

    public function __construct(EntityManagerInterface $em, BookRepository $bookRepository)
    {
        $this->em = $em;
        $this->bookRepository = $bookRepository;
    }

    public function find(UuidInterface $id): ?Book
    {
        return $this->bookRepository->find($id);
    }

    public function create(): Book
    {
        return new Book(Uuid::uuid4());
    }

    public function save(Book $book): Book
    {
        $this->em->persist($book);
        $this->em->flush();
        return $book;
    }

    public function reload(Book $book): Book
    {
        $this->em->refresh($book);
        return $book;
    }

    public function delete(Book $book)
    {
        $this->em->remove($book);
        $this->em->flush();
    }

    /**
     * Get the value of bookRepository
     */
    public function getRepository(): BookRepository
    {
        return $this->bookRepository;
    }
}
