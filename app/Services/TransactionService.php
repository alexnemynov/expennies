<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class TransactionService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function create(string $description, float $amount, \DateTime $date, User $user): Transaction
    {
        $transaction = new Transaction();

        $transaction->setUser($user);

        return $this;

//        return $this->update($category, $name);
    }
}