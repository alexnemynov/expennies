<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth;
use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Valitron\Validator;

class AuthController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly EntityManager $entityManager,
        private readonly Auth $auth,
    ) {
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/login.twig');
    }

    public function registerView(Request $request, Response $response): Response
    {
        return $this->twig->render($response, 'auth/register.twig');
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $v = new Validator($data);
        $v->rule('required', ['name', 'email', 'password', 'confirmPassword']);
        $v->rule('email', 'email');
        $v->rule('equals', 'password', 'confirmPassword');
        $v->rule(
            fn ($field, $value, $params, $fields) =>
            ! $this->entityManager->getRepository(User::class)->findBy([$field => $value]),
            'email'
        )->message('User with given email already exists.');
        $v->labels(array(
            'name' => 'Name',
            'email' => 'Email address',
            'password' => 'Password',
            'confirmPassword' => 'Confirm Password',
        ));
        if ($v->validate()) {
            echo "Yay! We're all good!";
        } else {
            throw new ValidationException($v->errors());
        }
        exit;
        $user = new User();

        $user->setEmail($data['email']);
        $user->setPassword(password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]));
        $user->setName($data['name']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $response;
    }

    public function logIn(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $v = new Validator($data);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', 'email');

        if (! $this->auth->attemptLogin($data)) {
            throw new ValidationException(['password' => ['You have entered wrong email or password.']]);
        };

        return $response->withHeader('Location', '/')->withStatus(302);
    }


    public function logOut(Request $request, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
