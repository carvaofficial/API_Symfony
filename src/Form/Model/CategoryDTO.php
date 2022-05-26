<?php

namespace App\Form\Model;

use App\Entity\Category;

class CategoryDTO
{
    public $id;
    public $name;

    public static function createFromBook(Category $category): self
    {
        $dto = new self();
        $dto->id = $category->getId();
        $dto->name = $category->getName();

        return $dto;
    }
}
