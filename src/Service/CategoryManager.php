<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

class CategoryManager
{
    private $em;
    private $categoryRepository;

    public function __construct(EntityManagerInterface $em, CategoryRepository $categoryRepository)
    {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
    }

    public function find(UuidInterface $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    public function create(): Category
    {
        return new Category(Uuid::uuid4());
    }

    public function persist(Category $category): Category
    {
        $this->em->persist($category);
        return $category;
    }

    public function save(Category $category): Category
    {
        $this->em->persist($category);
        $this->em->flush();
        return $category;
    }

    public function reload(Category $category): Category
    {
        $this->em->refresh($category);
        return $category;
    }

    /**
     * Get the value of categoryRepository
     */
    public function getRepository(): CategoryRepository
    {
        return $this->categoryRepository;
    }
}
