<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\DataObjects\TransactionData;
use App\RequestValidators\TransactionImportRequestValidator;
use App\Services\CategoryService;
use App\Services\TransactionImporterService;
use App\Services\TransactionService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class TransactionImporterController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly TransactionService $transactionService,
        private readonly TransactionImporterService $transactionImporterService,
        private readonly CategoryService $categoryService,
    ) {
    }
    public function import(Request $request, Response $response, array $args): Response
    {
        /** @var UploadedFileInterface $file */
        $file     = $this->requestValidatorFactory->make(TransactionImportRequestValidator::class)->validate(
            $request->getUploadedFiles()
        )['importFile'];

        $user = $request->getAttribute('user');
        $stream = fopen($file->getStream()->getMetadata('uri'), 'r');

        fgetcsv($stream);

        while (($transaction = fgetcsv($stream)) !== false) {
            $data = $this->transactionImporterService->parseTransaction($transaction, $this->categoryService);
            $transactionData = new TransactionData(
                $data['description'],
                $data['amount'],
                $data['date'],
                $data['category']
            );

            $this->transactionService->create($transactionData, $user);
        }

        return $response;
    }
}