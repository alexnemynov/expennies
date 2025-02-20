<?php

declare(strict_types = 1);

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class TwoFactorLoginRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $v = new Validator($data);

        $v->rule('required', ['email', 'code'])->message('Required field');;
        $v->rule('email', 'email')->message('Invalid Two-Factor indicator');;

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}