<?php

namespace App\Service;

use App\Entity\Categorie;
use App\Entity\Categories;
use App\Entity\Livre;
use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryService
{
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
    }

    public function fetchCategories(): array
    {
        $url = 'https://openlibrary.org/subjects.json';
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();

        return $data;
    }

    public function fetchBooksByCategory(Categories $categorie, int $limit = 100): array
    {
        $url = sprintf('https://openlibrary.org/subjects/%s.json?limit=%d', $categorie->getCategorie(), $limit);
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();

        return $data;
    }
}
