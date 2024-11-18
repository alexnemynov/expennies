<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\UserInterface;
use App\Contracts\UserProviderServiceInterface;
use App\DataObjects\RegisterUserData;
use App\Entity\User;
use Doctrine\ORM\EntityManager;

class UserProviderService implements UserProviderServiceInterface
{
    private ?UserInterface $user = null;

    public function __construct(private readonly EntityManager $entityManager)
    {
    }

    public function getById(int $id): UserInterface
    {
        return $this->entityManager->getRepository(User::class)->find($id);
    }

    public function getByCredentials(array $credentials): UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
    }

    public function createUser(RegisterUserData $data): UserInterface
    {
        $user = new User();

        $user->setEmail($data->email);
        $user->setPassword(password_hash($data->password, PASSWORD_BCRYPT, ['cost' => 12]));
        $user->setName($data->name);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
