<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class AuthController
{
    public function __construct(private readonly Twig $twig, private readonly EntityManager $entityManager)
    {
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function logIn(Request $request, Response $response): Response
    {
        return $response;
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $user = new User();

        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]));
        $user->setName($data['name']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();



        return $response;
    }
}
