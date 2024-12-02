<?php

declare(strict_types = 1);

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Entity\Receipt;
use App\RequestValidators\UploadReceiptRequestValidator;
use App\Services\ReceiptService;
use League\Flysystem\Filesystem;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;

class ReceiptController
{
    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ReceiptService $receiptService,
    )
    {
    }

    public function store(Request $request, Response $response, array $args): Response
    {
        /** @var UploadedFileInterface $file */
        $file = $this->requestValidatorFactory
            ->make(UploadReceiptRequestValidator::class)
            ->validate($request->getUploadedFiles())['receipt'];
        $filename = $file->getClientFilename();

        $id = (int) $args['id'];

        if (! $id || ! ($transaction = $this->transactionService->getById($id))) {
            return $response->withStatus(404);
        }

        $this->filesystem->write('receipts/' . $filename, $file->getStream()->getContents());

        $this->receiptService->create($transaction, $filename);

        return $response;
    }
}
