<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        if($this->isGranted('ROLE_USER')){
            $post = new Post();
            $post->setUser($this->getUser());

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                /** @var Post $post */
                $post = $form->getData();
                $post->setDatePost(new \DateTime('now'));

                $post->getImage()->upload();

                $em->persist($post);
                $em->flush();
            }

        }

        $posts = $em->getRepository('AppBundle:Post')->findBy([], ['id' => 'DESC']);


        return $this->render('AppBundle:Default:index.html.twig', array(
            'form' => isset($form) ? $form->createView() : null,
            'posts' => $posts
        ));
    }

}
