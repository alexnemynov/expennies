<?php

declare(strict_types=1);

namespace App\Contracts;

interface UserProviderServiceInterface
{
    public function getById(int $id): ?UserInterface;
    public function getByCredentials(array $credentials): ?UserInterface;
}
