<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BudgetService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getBudgetById(int $budgetId): ?array
    {
        // Make a request to the budget microservice to fetch the budget by ID
        $response = $this->client->request('GET', 'http://127.0.0.1:8000/budget/' . $budgetId);

        // Decode the JSON response
        return $response->toArray();
    }

    public function addTransactionToBudget(int $budgetId, array $transactionData): bool
    {
        // Make a request to the budget microservice to add a transaction to the selected budget
        $response = $this->client->request('POST', 'http://127.0.0.1:8000/budget/' . $budgetId . '/add-transaction', [
            'json' => $transactionData
        ]);

        // Check if the request was successful (status code 200)
        return $response->getStatusCode() === 200;
    }

    public function getBudgetId(int $budgetId): ?int
    {
        // Make a request to the budget microservice to fetch the budget ID
        $response = $this->client->request('GET', 'http://127.0.0.1:8000/budget/' . $budgetId);

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        // Decode the JSON response
        $data = $response->toArray();

        // Extract the budget ID from the response
        return $data['id'] ?? null;
    }


//getallBudgets
    public function consumeAPI(): array
    {
        // Make an HTTP GET request to retrieve income and budget data from budget microservice
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://127.0.0.1:8000/budget/api/getBudgets');

        // Check if the request was successful
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            throw new \RuntimeException('Error fetching income and budget data');
        }

        // Decode the JSON response received from the budget microservice
        return $response->toArray();
    }



    public function getBudgetIdWithName(string $categories): ?int
    {
        // Get all budgets from the budget microservice
        $budgets = $this->consumeAPI();
    
        // Search for the budget with the provided name
        foreach ($budgets as $budget) {
            if ($budget['categories'] === $categories) {
                return $budget['id'];
            }
        }
    
        return null; // Budget not found
    }
    





}
