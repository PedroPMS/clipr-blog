<?php

namespace App\Utils;

use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;

class ValidationErrorsHandler
{
    public function __invoke(FormInterface $form): View
    {
        $errors = [];
        foreach ($form->getErrors(true, true) as $error) {
            $errors[$error->getOrigin()->getName()] = $error->getMessage();
        }

        return View::create([
            'message' => 'The given data was invalid.',
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
