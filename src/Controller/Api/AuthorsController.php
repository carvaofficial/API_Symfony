<?php

namespace App\Controller\Api;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Service\Author\AuthorFormProcessor;
use App\Service\Author\DeleteAuthor;
use App\Service\Author\GetAuthor;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorsController extends AbstractFOSRestController
{
    /**
     *@Rest\Get(path="/authors")
     *@Rest\View(serializerGroups={"book"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getAction(AuthorRepository $authorRepository)
    {
        return $authorRepository->findAll();
    }

    public function postAction(AuthorFormProcessor $bfp, Request $request)
    {
        $author = Author::create('');
        [$author, $error] = ($bfp)($request);

        return View::create($author ?? $error, $author ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Get(path="/authors/{id}")
     *@Rest\View(serializerGroups={"author"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function getSingleAction(string $id, GetAuthor $getAuthor)
    {
        /**
         * Método para poder llamar al método __invoke de una clase y pasarle parámetros
         */
        $author = ($getAuthor)($id);
        return !$author ? View::create('Author not found', Response::HTTP_BAD_REQUEST) : $author;
    }

    /**
     *@Rest\Post(path="/authors/{id}")
     *@Rest\View(serializerGroups={"author"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function editAction(string $id, AuthorFormProcessor $bfp, GetAuthor $getAuthor, Request $request)
    {
        $author = ($getAuthor)($id);
        if (!$author) {
            return View::create('Author not found', Response::HTTP_BAD_REQUEST);
        }

        [$author, $error] = ($bfp)($request, $id);

        return View::create($author ?? $error, $author ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST);
    }

    /**
     *@Rest\Delete(path="/authors/{id}")
     *@Rest\View(serializerGroups={"author"}, serializerEnableMaxDepthChecks=true)
     *  */
    public function deleteAction(string $id, DeleteAuthor $deleteAuthor, Request $request)
    {
        try {
            ($deleteAuthor)($id);
        } catch (\Throwable $th) {
            return View::create('Author not found', Response::HTTP_BAD_REQUEST);
        }

        return View::create(null, Response::HTTP_NO_CONTENT);
    }
}
