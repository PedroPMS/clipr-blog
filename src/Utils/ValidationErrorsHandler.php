<?php

namespace App\Utils;

use Symfony\Component\Form\FormInterface;

class ValidationErrorsHandler
{
    public function __invoke(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return [
            'message' => 'The given data was invalid.',
            'errors' => $errors
        ];
    }
}
