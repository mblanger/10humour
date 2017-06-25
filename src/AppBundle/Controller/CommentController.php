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
        return $this->render('AppBundle:Comment:comment.html.twig', array(
            'post' => $post,
            'form' => $this->getForm($post)->createView()
        ));
    }

    public function postCommentAction(Request $request, Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->getForm($post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setDatePost(new \DateTime('now'));

            $post->addComment($comment);

            $em->persist($post);
            $em->flush();

            $this->addFlash('notice', 'Commentaire posté !');

            return $this->redirectToRoute('comment', ['post' => $post->getId()]);
        }
    }

    private function getForm(Post $post)
    {
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $comment->setPost($post);

        return $this->createForm(CommentType::class, $comment);
    }

}
