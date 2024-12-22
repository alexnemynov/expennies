<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\SimpleCache\CacheInterface;
use Slim\Views\Twig;

class HomeController
{
    public function __construct(private readonly Twig $twig, private readonly CacheInterface $cache)
    {
    }

    public function index(Response $response): Response
    {
        $this->cache->set('a', 1, 5);
        $this->cache->setMultiple(['b' => 2, 'c' => 3], 15);

        var_dump($this->cache->get('a'));
        var_dump($this->cache->getMultiple(['b', 'c']));

        return $this->twig->render($response, 'dashboard.twig');
    }
}
