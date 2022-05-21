<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LibraryController extends AbstractController
{
    // private $logger;

    // public function __construct(LoggerInterface $logger)
    // {
    //     $this->logger = $logger;
    // }


    /**
     * @Route("/library/list", name="library_list")
     */
    public function list(Request $request, LoggerInterface $logger)
    {
        // $this->logger->info('List action called (service as property of class)');
        $logger->info('List action called (service as argument)');

        $title = $request->get('title', 'Alegría');

        $response = new JsonResponse();
        $response->setData([
            'success' => true,
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Hacia rutas salvajes'
                ],
                [
                    'id' => 2,
                    'title' => 'El nombre del viento'
                ],
                [
                    'id' => 3,
                    'title' => $title
                ],
            ]
        ]);

        return $response;
    }
}
