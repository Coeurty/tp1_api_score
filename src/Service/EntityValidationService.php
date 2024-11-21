<?php

namespace App\Service;

use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidationService
{
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $entity): array
    {
        $errors = $this->validator->validate($entity, null);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return $errorMessages;
        }

        return [];
    }
}
