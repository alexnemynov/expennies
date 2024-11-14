<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entity\User;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Valitron\Validator;

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
        // 1. Validate request data
        $data = $request->getParsedBody();

        $v = new Validator($data);
        $v->rule('required', ['email', 'password']);
        $v->rule('email', 'email');

        // 2. Check the user credentials
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (! $user || ! password_verify($data['password'], $user->getPassword())) {
            throw new ValidationException(['password' => ['You have entered wrong email or password.']]);
        }

        // 3. Save user id in the session
        session_regenerate_id();
        $_SESSION['user'] = $user->getId();

        // 4. Redirect user to home page
        return $response->withHeader('Location', '/')->withStatus(302);
    }


    public function logOut(Request $request, Response $response): Response
    {
        // TODO

        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
