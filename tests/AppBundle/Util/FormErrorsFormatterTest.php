<?php

namespace Tests\AppBundle\Util;

use AppBundle\FormTools\FormErrorsFormatter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\Test\TypeTestCase;

class FormErrorsFormatterTest extends TypeTestCase
{
    public function testGetErrorMessages()
    {
        $type = new FormType();
        $form = $this->factory->create(FormType::class);
        $form->addError(new FormError('error'));²

        $errorsFormatter = new FormErrorsFormatter();
        $errors = $errorsFormatter->getErrorMessages($form);

        $this->assertEquals(['error'], $errors);
    }
}