<?php

namespace App\Controller\Api;

use App\Form\Model\CategoryDTO;
use App\Form\Type\CategoryFormType;
use App\Service\CategoryManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

class CategoriesController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/categories")
     *@Rest\View(serializerGroups={"category"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getAction(CategoryManager $categoryManager)
    {
        return $categoryManager->getRepository()->findAll();
    }

    /**
     *@Rest\Post(path="/categories")
     *@Rest\View(serializerGroups={"category"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function postAction(CategoryManager $categoryManager, Request $request)
    {
        $categoryDTO = new CategoryDTO();
        $form = $this->createForm(CategoryFormType::class, $categoryDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categoryManager->create();
            $category->setName($categoryDTO->name);
            return $category;
        }

        return $form;
    }
}
