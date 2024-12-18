<?php

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\Contracts\UserProfileServiceInterface;
use App\DataObjects\UserProfileData;
use App\RequestValidators\UpdateProfileRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ProfileController
{
    public function __construct(
        private readonly Twig $twig,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly UserProfileServiceInterface $userProfileService,
    ) {
    }

    public function index(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');

        return $this->twig->render(
            $response,
            'profile/index.twig',
            ['profileData' => $this->userProfileService->get($user)]
        );
    }

    public function update(Request $request, Response $response): Response
    {
        $user = $request->getAttribute('user');
        $data = $this->requestValidatorFactory->make(UpdateProfileRequestValidator::class)->validate(
            $request->getParsedBody()
        );

        $this->userProfileService->update(
            $user,
            new UserProfileData(
                $user->getEmail(),
                $data['name'],
                (bool) $data['twoFactor'] ?? false
            )
        );

        return $response;
    }
}