<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Views\Twig;

class ValidationErrorsMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly Twig $twig)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (! empty($_SESSION['errors'])) {
            $errors = $_SESSION['errors'];

            // Если есть ошибки, передаем их во все шаблоны Twig глобально
            $this->twig->getEnvironment()->addGlobal('errors', $errors);

            unset($_SESSION['errors']);
        }

        return $handler->handle($request);
    }
}
