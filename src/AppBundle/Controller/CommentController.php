<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;
use AppBundle\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    public function commentAction(Request $request, Post $post)
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        dump($form->getData());

        return $this->render('AppBundle:Comment:comment.html.twig', array(
            'post' => $post,
            'form' => $form->createView()
        ));
    }

}
