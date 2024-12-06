<?php

declare(strict_types=1);

namespace App\Services;

class TransactionImporterService
{

    public function parseTransaction(array $transactionRow, CategoryService $categoryService): array
    {
        [$date, $description, $category, $amount] = $transactionRow;
        $amount = (float) str_replace(['$', ','], '', $amount);
        $category = $categoryService->getByName($category);

        return [
            'date' => new \DateTime($date),
            'description' => $description,
            'category' => $category,
            'amount' => $amount,
        ];
    }
}