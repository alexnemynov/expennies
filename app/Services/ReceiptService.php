<?php

declare(strict_types = 1);

namespace App\Services;

use App\Entity\Receipt;
use App\Entity\Transaction;
use Doctrine\ORM\EntityManager;

class ReceiptService
{
    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function create($transaction, string $filename, string $storageFilename, string $mediaType): Receipt
    {
        $receipt = new Receipt();

        $receipt->setTransaction($transaction);
        $receipt->setFilename($filename);
        $receipt->setStorageFilename($storageFilename);
        $receipt->setCreatedAt(new \DateTime());
        $receipt->setMediaType($mediaType);

        $this->entityManager->persist($receipt);
        $this->entityManager->flush();

        return $receipt;
    }

    public function getById(int $id)
    {
        return $this->entityManager->find(Receipt::class, $id);
    }

    public function delete(int $id): void
    {
        $receipt = $this->entityManager->find(Receipt::class, $id);

        $this->entityManager->remove($receipt);
        $this->entityManager->flush();
    }
}