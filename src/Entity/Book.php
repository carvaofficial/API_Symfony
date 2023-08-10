<?php

namespace App\Entity;

use App\Entity\Book\Score;
use App\Event\Book\BookCreatedEvent;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Book
{
    private UuidInterface $id;

    private string $title;

    private ?string $image;

    private ?string $description;

    private ?Score $score;

    private Collection $categories;

    private Collection $authors;

    private array $domainEvents = [];

    public function __construct(UuidInterface $uuidInterface)
    {
        $this->id = $uuidInterface;
        $this->score = Score::create();
        $this->categories = new ArrayCollection();
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getScore(): ?Score
    {
        return $this->score;
    }

    public function setScore(?Score $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    public function addDomainEvent(Event $event): void
    {
        $this->domainEvents[] = $event;
    }

    public function pullDomainEvents(): array
    {
        return $this->domainEvents;
    }

    public static function create(): self
    {
        $book = new self(Uuid::uuid4());
        $book->addDomainEvent(new BookCreatedEvent($book->getId()));
        return $book;
    }

    public function update(
        string $title,
        ?string $image,
        ?string $description,
        ?Score $score,
        array $categories,
        array $authors
    ) {
        $this->title = $title;
        $this->image = $image;
        $this->description = $description;
        $this->score = $score;
        $this->updateCategories(...$categories);
        $this->updateAuthors(...$authors);
    }

    public function updateCategories(Category ...$categories)
    {
        /**
         * @var Category[]|ArrayCollection $originalCategories
         */
        $originalCategories = new ArrayCollection();
        foreach ($this->categories as $category) {
            $originalCategories->add($category);
        }

        // Remove categories
        foreach ($originalCategories as $originalCategory) {
            if (!\in_array($originalCategory, $categories)) {
                $this->removeCategory($originalCategory);
            }
        }

        // Add categories
        foreach ($categories as $newCategory) {
            if (!$originalCategories->contains($newCategory)) {
                $this->addCategory($newCategory);
            }
        }
    }

    public function updateAuthors(Author ...$authors)
    {
        /**
         * @var Author[]|ArrayCollection $originalAuthors
         */
        $originalAuthors = new ArrayCollection();
        foreach ($this->authors as $category) {
            $originalAuthors->add($category);
        }

        // Remove authors
        foreach ($originalAuthors as $originalAuthor) {
            if (!\in_array($originalAuthor, $authors)) {
                $this->removeAuthor($originalAuthor);
            }
        }

        // Add authors
        foreach ($authors as $newAuthor) {
            if (!$originalAuthors->contains($newAuthor)) {
                $this->addAuthor($newAuthor);
            }
        }
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? 'Libro';
    }
}
