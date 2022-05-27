<?php

namespace App\Controller\Api;

use App\Service\CategoryManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;


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
}
