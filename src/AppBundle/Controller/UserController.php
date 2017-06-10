<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\FormTools\FormErrorsFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    public function registerAction(Request $request)
    {
        $form = $this->createForm(RegisterType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var User $user */
            $user = $form->getData();

            /** @var UserPasswordEncoder $encoder */
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPlainPassword($encoded);
        }

        /** @var FormErrorsFormatter $formErrorsFormatter */
        $formErrorsFormatter = $this->get('app.formerrorsformatter');
        $errors = $formErrorsFormatter->getErrorMessages($form);

        return $this->render('AppBundle:User:register.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors
        ));
    }

    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $this->addFlash('error', $error);

        return $this->render('AppBundle:User:login.html.twig', array(
            // ...
        ));
    }

    public function logoutAction()
    {

    }

}
