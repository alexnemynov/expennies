<?php

declare(strict_types=1);

namespace App;

use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class UserProviderService implements UserProviderServiceInterface
{
    private ?UserInterface $user = null;

    public function __construct(EntityManager $entityManager)
    {
    }

    public function getById(int $id): UserInterface
    {
        return $user = $this->entityManager->getRepository(User::class)->find($id);
    }

    public function getByCredentials(array $credentials): UserInterface
    {

    }
}