<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommentController extends Controller
{
    public function commentAction(Request $request, Post $post)
    {
        return $this->render('AppBundle:Comment:comment.html.twig', array(
            'post' => $post
        ));
    }

}
