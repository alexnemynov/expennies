<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\TransactionData;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class TransactionService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function create(TransactionData $data, User $user): Transaction
    {
        $transaction = new Transaction();

        $transaction->setUser($user);

        return $this->update($transaction, $data);
    }

    private function update(Transaction $transaction, TransactionData $data): Transaction
    {
        $transaction->setDescription($data->description);
        $transaction->setAmount($data->amount);
        $transaction->setDate($data->date);
        $transaction->setCategory($data->category);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        return $transaction;
    }

    public function getById(int $id): ?Transaction
    {
        return $this->entityManager->find(Transaction::class, $id);
    }

    public function delete(int $id): void
    {
        $transaction = $this->entityManager->find(Transaction::class, $id);

        $this->entityManager->remove($transaction);
        $this->entityManager->flush();
    }

    public function getAll()
    {
        return $this->entityManager->getRepository(Transaction::class)->findAll();
    }
}