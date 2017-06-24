<?php

namespace AppBundle\FormTools;

use Symfony\Component\Form\FormInterface;

class FormErrorsFormatter  {

    public function getErrorMessages(FormInterface $form) {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors = array_merge($this->getErrorMessages($child), $errors);
            }
        }

        return $errors;
    }
}