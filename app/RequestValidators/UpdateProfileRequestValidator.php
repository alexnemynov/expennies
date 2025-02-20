<?php

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use Valitron\Validator;

class UpdateProfileRequestValidator implements RequestValidatorInterface
{

    public function validate(array $data): array
    {
        $v = new Validator($data);
        $v->rule('required', 'name')->message('Required field');;
        $v->rule('integer', 'twoFactor');

        if (! $v->validate()) {
            throw new ValidationException($v->errors());
        }

        return $data;
    }
}