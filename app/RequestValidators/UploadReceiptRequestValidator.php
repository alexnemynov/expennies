<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadReceiptRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = $data['receipt'] ?? null;

        // TODO

        return $data;
    }
}