<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Service\BudgetService;
use App\Repository\TransactionRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;


/**
 * @Route("/transaction")
 */
class TransactionController extends AbstractController
{
    
    /**
     * @Route("/", name="app_transaction_index", methods={"GET"})
     */
    public function index(TransactionRepository $transactionRepository): JsonResponse
    {
        $transactions = $transactionRepository->findAll();
        return $this->json($transactions, Response::HTTP_OK, [], ['groups' => 'transaction']);
    }


     /**
 * @Route("/{id}", name="app_transaction_show", methods={"GET"})
 */
public function show(int $id, TransactionRepository $transactionRepository): JsonResponse
{
    $transaction = $transactionRepository->find($id);

    if (!$transaction) {
        return new JsonResponse(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
    }

    return $this->json($transaction, Response::HTTP_OK, [], ['groups' => 'transaction']);
}

    

    /**
     * @Route("/new", name="app_transaction_new", methods={"POST"})
     */
    public function new(Request $request, TransactionRepository $transactionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Assuming you're sending necessary data for creating a new transaction from the request
        $nomTransaction = $data['nomtransaction'] ?? null;
        $montantTransaction = $data['montantTransaction'] ?? null;
        $budgetId = $data['budgetId'] ?? null;

        // Validation checks for required fields
        if ($nomTransaction === null || $montantTransaction === null || $budgetId === null) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        // Create a new Transaction entity
        $transaction = new Transaction();
        $transaction->setNomtransaction($nomTransaction);
        $transaction->setMontantTransaction($montantTransaction);
        $transaction->setBudgetId($budgetId);

        // Persist the transaction entity
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($transaction);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Transaction created successfully'], Response::HTTP_CREATED);
    }

   /**
 * @Route("/edit/{id}", name="app_transaction_edit", methods={"PUT"})
 */
public function edit(int $id, Request $request, TransactionRepository $transactionRepository): JsonResponse
{
    // Find the transaction entity by its ID
    $transaction = $transactionRepository->find($id);
    
    // Check if the transaction exists
    if (!$transaction) {
        return new JsonResponse(['error' => 'Transaction not found'], Response::HTTP_NOT_FOUND);
    }

    // Assuming you're sending necessary data for editing the transaction from the request
    $data = json_decode($request->getContent(), true);
    $nomtransaction = $data['nomtransaction'] ?? null;
    $montantTransaction = $data['montantTransaction'] ?? null;

    // Validation checks for required fields
    if ($nomtransaction === null || $montantTransaction === null) {
        return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
    }

    // Update the transaction entity
    $transaction->setNomtransaction($nomtransaction);
    $transaction->setMontantTransaction($montantTransaction);

    // Persist the updated transaction entity
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return new JsonResponse(['message' => 'Transaction updated successfully'], Response::HTTP_OK);
}



/**
     * @Route("/{id}", name="app_transaction_delete", methods={"DELETE"})
     */
 
    public function delete(Request $request, Transaction $transaction): JsonResponse
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($transaction);
        $entityManager->flush();

        return new JsonResponse(['message' => 'transaction deleted successfully'], Response::HTTP_OK);
    }






/*
    private $budgetService;

    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }
*/
    
 



/**
     * @Route("/consume/getBudgets", name="consume_income_and_budget_data")
     */
    public function consumeAPI(): Response
    {
        $budgetUrl = $_ENV['Budget_url'];

        // Make an HTTP GET request to retrieve income and budget data from budget microservice
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $budgetUrl.'/api/getBudgets');

        // Check if the request was successful
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return new Response('Error fetching income and budget data', $response->getStatusCode());
        }

        // Decode the JSON response received from the budget microservice
        $data = $response->toArray();

      
        // Encode the array data to JSON
        $jsonData = json_encode($data);

        // Return the JSON response received from the budget microservice
        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }



    
private $httpClient;

public function __construct(HttpClientInterface $httpClient)
{
    

    $this->httpClient = $httpClient;
}


    /**
 * @Route("/add/{budgetId}", name="app_transaction_new", methods={"POST"})
 */
public function add(int $budgetId, Request $request, TransactionRepository $transactionRepository): JsonResponse
{
    // Assuming you're sending necessary data for creating a new transaction from the request
    $data = json_decode($request->getContent(), true);
    $nomtransaction = $data['nomtransaction'] ?? null;
    $montantTransaction = $data['montantTransaction'] ?? null;

    // Validation checks for required fields
    if ($nomtransaction === null || $montantTransaction === null) {
        return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
    }

    // Create a new Transaction entity
    $transaction = new Transaction();
    $transaction->setNomtransaction($nomtransaction);
    $transaction->setMontantTransaction($montantTransaction);
    $transaction->setBudgetId($budgetId);

    // Persist the transaction entity
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($transaction);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Transaction created successfully'], Response::HTTP_CREATED);
}




/**
 * @Route("/budgets/{budgetId}", name="get_transactions_for_budget", methods={"GET"})
 */
public function getTransactionsForBudget(int $budgetId, TransactionRepository $transactionRepository): JsonResponse
{
    // Retrieve transactions for the specified budget
    $transactions = $transactionRepository->findBy(['budgetId' => $budgetId]);

    // Serialize transactions to an array
    $data = [];
    foreach ($transactions as $transaction) {
        $data[] = [
            'id' => $transaction->getId(),
            'nomtransaction' => $transaction->getNomtransaction(),
            'montantTransaction' => $transaction->getMontantTransaction(),
        ];
    }

    // Return transactions as JSON response
    return new JsonResponse($data);

}



/**
 * @Route("/budgetsWithName/{categories}", name="get_transactions_for_budget_withname", methods={"GET"})
 */
public function getTransactionsForBudgetWithName(string $categories, Request $request, TransactionRepository $transactionRepository, BudgetService $budgetService): JsonResponse
{
    // Retrieve the budget ID corresponding to the provided categories
    $budgetId = $budgetService->getBudgetIdWithName($categories);

    // Check if the budget with the provided name exists
    if ($budgetId === null) {
        return new JsonResponse(['error' => 'Budget not found'], Response::HTTP_NOT_FOUND);
    }

    // Retrieve transactions for the specified budget
    $transactions = $transactionRepository->findBy(['budgetId' => $budgetId]);

    // Serialize transactions to an array
    $data = [];
    foreach ($transactions as $transaction) {
        $data[] = [
            'id' => $transaction->getId(),
            'nomtransaction' => $transaction->getNomtransaction(),
            'montantTransaction' => $transaction->getMontantTransaction(),
            // Add more fields as needed
        ];
    }

    // Return transactions as JSON response
    return new JsonResponse($data);
}















































 

}
