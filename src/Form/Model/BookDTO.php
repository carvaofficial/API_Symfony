<?php

namespace App\Form\Model;

use App\Entity\Book;

class BookDTO
{
    public ?string $title = null;
    public ?string $base64Image = null;
    /**
     *
     * @var CategoryDTO[]|null
     */
    public ?array $categories = [];

    public function __construct()
    {
        $this->categories = [];
    }

    public static function createFromBook(Book $book): self
    {
        $dto = new self();
        $dto->title = $book->getTitle();

        return $dto;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBase64Image(): ?string
    {
        return $this->base64Image;
    }

    /**
     *
     * @return CategoryDTO[]|null
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }
}
