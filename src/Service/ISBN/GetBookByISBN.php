<?php

namespace App\Service\ISBN;

use App\Model\DTO\ISBN\GetBookByISBNResponse;
use App\Service\Utils\HttpClientInterface;
use Exception;

class GetBookByISBN
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function __invoke(string $isbn): GetBookByISBNResponse
    {
        $response = $this->httpClient->request('GET', sprintf('https://openlibrary.org/isbn/%s.json', $isbn));

        if ($response->getStatusCode() !== 200) {
            throw new Exception('Error recuperando el libro');
        }

        $json = json_decode($response->getContent(), true);

        return new GetBookByISBNResponse(
            $json['title'],
            $json['number_of_pages'],
            $json['publish_date']
        );
    }
}
