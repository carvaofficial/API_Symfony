<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Form\Model\CategoryDTO;
use App\Form\Type\CategoryFormType;
use App\Repository\CategoryRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class CategoriesController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/categories")
     *@Rest\View(serializerGroups={"category"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getAction(CategoryRepository $categoryRepository)
    {
        return $categoryRepository->findAll();
    }

    /**
     *@Rest\Post(path="/categories")
     *@Rest\View(serializerGroups={"category"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function postAction(CategoryRepository $categoryRepository, Request $request)
    {
        $categoryDTO = new CategoryDTO();
        $form = $this->createForm(CategoryFormType::class, $categoryDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = Category::create($categoryDTO->getName());
            $categoryRepository->save($category);
            return $category;
        }

        return $form;
    }
}
