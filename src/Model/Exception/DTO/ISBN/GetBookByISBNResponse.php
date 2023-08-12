<?php

namespace App\Model\DTO\ISBN;

class GetBookByISBNResponse
{
    private string $title;

    private int $numberOfPages;

    private string $publishDate;

    public function __construct(
        string $title,
        int $numberOfPages,
        string $publishDate
    ) {
        $this->title = $title;
        $this->numberOfPages = $numberOfPages;
        $this->publishDate = $publishDate;
    }

    /**
     * Get the value of title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Get the value of numberOfPages
     */
    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    /**
     * Get the value of publishDate
     */
    public function getPublishDate(): string
    {
        return $this->publishDate;
    }
}
