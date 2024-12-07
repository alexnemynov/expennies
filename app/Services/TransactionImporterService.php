<?php

declare(strict_types=1);

namespace App\Services;

class TransactionImporterService
{

    public function parseTransaction(array $transactionRow, array $categories): array
    {
        [$date, $description, $category, $amount] = $transactionRow;
        $amount = (float) str_replace(['$', ','], '', $amount);
        $category = $categories[$category] ?? null;

        return [
            'date' => new \DateTime($date),
            'description' => $description,
            'category' => $category,
            'amount' => $amount,
        ];
    }
}