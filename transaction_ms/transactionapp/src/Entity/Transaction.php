<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TransactionRepository::class)
 */
class Transaction
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
    * @Groups({"transaction"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
         * @Groups({"transaction"})
     */
    private $nomtransaction;

    /**
     * @ORM\Column(type="float")
         * @Groups({"transaction"})
     */
    private $montantTransaction;

    /**
     * @ORM\Column(type="integer")
         * @Groups({"transaction"})
     */
    private $budgetId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomtransaction(): ?string
    {
        return $this->nomtransaction;
    }

    public function setNomtransaction(string $nomtransaction): self
    {
        $this->nomtransaction = $nomtransaction;

        return $this;
    }

    public function getMontantTransaction(): ?float
    {
        return $this->montantTransaction;
    }

    public function setMontantTransaction(float $montantTransaction): self
    {
        $this->montantTransaction = $montantTransaction;

        return $this;
    }

    public function getBudgetId(): ?int
    {
        return $this->budgetId;
    }

    public function setBudgetId(int $budgetId): self
    {
        $this->budgetId = $budgetId;

        return $this;
    }
}
