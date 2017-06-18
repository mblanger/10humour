<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Vote;
use AppBundle\Form\PostType;
use AppBundle\Form\VoteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        if ($this->isGranted('ROLE_USER')) {
            $post = new Post();
            $post->setUser($this->getUser());

            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var Post $post */
                $post = $form->getData();
                $post->setDatePost(new \DateTime('now'));

                $post->getImage()->upload();

                $em->persist($post);
                $em->flush();
            }

        }

        $posts = $em->getRepository('AppBundle:Post')->findBy([], ['id' => 'DESC']);

        $vote = new Vote();
        $vote->setUser($this->getUser());
        $voteForm = $this->createForm(VoteType::class, $vote);
        $voteForm->handleRequest($request);

        if ($this->isGranted('ROLE_USER') && $voteForm->isSubmitted() && $voteForm->isValid()) {

            /** @var Vote $vote */
            $vote = $voteForm->getData();
            $em->getRepository('AppBundle:Vote')->removeVotesByUserAndPost($this->getUser(), $vote->getPost());

            $em->persist($vote);
            $em->flush();

            $this->addFlash('notice', 'Votre vote à été pris en compte');

            return $this->redirectToRoute('index');
        }

        $nb = count($posts);
        for ($i = 0; $i < $nb; $i++) {
            $posts[$i]->upvotes = $em->getRepository('AppBundle:Vote')->countVotesByPost($posts[$i]);
            $posts[$i]->downvotes = $em->getRepository('AppBundle:Vote')->countVotesByPost($posts[$i], false);
        }



        return $this->render('AppBundle:Default:index.html.twig', array(
            'form' => isset($form) ? $form->createView() : null,
            'voteForm' => $voteForm->createView(),
            'posts' => $posts
        ));
    }

}
