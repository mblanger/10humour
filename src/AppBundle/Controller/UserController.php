<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\FormTools\FormErrorsFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserController extends Controller
{
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        $this->generateSalt();

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();
            /** @var EncoderFactory $encoder */
            $encoder = $this->get('security.encoder_factory');
            $encoded = $encoder->getEncoder($user);
//            $encoded->encodePassword($user->getPlainPassword(), )
        }
        /** @var FormErrorsFormatter $formErrorsFormatter */
        $formErrorsFormatter = $this->get('app.formerrorsformatter');
        $errors = $formErrorsFormatter->getErrorMessages($form);

        return $this->render('AppBundle:User:register.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors
        ));
    }

    private function generateSalt()
    {
        $salt = '';

        for($i=0;$i<32;$i++){
            $salt .= chr(rand(0,256));
        }

        return $salt;
    }

    public function loginAction()
    {
        return $this->render('AppBundle:User:login.html.twig', array(
            // ...
        ));
    }

    public function logoutAction()
    {

    }

}
