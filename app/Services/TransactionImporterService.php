<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\TransactionData;
use App\Entity\Transaction;
use App\Entity\User;
use Clockwork\Clockwork;
use Clockwork\Request\LogLevel;
use Doctrine\ORM\EntityManager;

class TransactionImporterService
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly TransactionService $transactionService,
        private readonly EntityManager $entityManager,
        private readonly Clockwork $clockwork
    )
    {
    }

    public function importFromFile(string $file, User $user): void
    {
        $stream = fopen($file, 'r');
        $categories = $this->categoryService->getAllKeyedByName();

        fgetcsv($stream);

        $this->clockwork->log(LogLevel::DEBUG, 'Memory usage before import ' .
            memory_get_usage());
        $this->clockwork->log(LogLevel::DEBUG, 'Unit of work before import ' .
            $this->entityManager->getUnitOfWork()->size());

        $count = 1;
        $batchSize = 250;
        while (($transaction = fgetcsv($stream)) !== false) {
            $data = $this->parseTransaction($transaction, $categories);
            $transactionData = new TransactionData(
                $data['description'],
                $data['amount'],
                $data['date'],
                $data['category']
            );

            $this->transactionService->create($transactionData, $user);

            if ($count % $batchSize === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear(Transaction::class);

                $count = 1;
            } else {
                $count++;
            }
        }

        if ($count > 1) {
            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        $this->clockwork->log(LogLevel::DEBUG, 'Memory usage after import ' .
            memory_get_usage());
        $this->clockwork->log(LogLevel::DEBUG, 'Unit of work after import ' .
            $this->entityManager->getUnitOfWork()->size());
    }

    private function parseTransaction(array $transactionRow, array $categories): array
    {
        [$date, $description, $category, $amount] = $transactionRow;
        $amount = (float) str_replace(['$', ','], '', $amount);
        $category = $categories[strtolower($category)] ?? null;

        return [
            'date' => new \DateTime($date),
            'description' => $description,
            'category' => $category,
            'amount' => $amount,
        ];
    }
}