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

        $vote = new Vote();
        $vote->setUser($this->getUser());
        $voteForm = $this->createForm(VoteType::class, $vote);
        $voteForm->handleRequest($request);

        $form = $this->getPostForm();


        if ($this->isGranted('ROLE_USER')) {


            if ($voteForm->isSubmitted() && $voteForm->isValid()) {

                /** @var Vote $vote */
                $vote = $voteForm->getData();
                $em->getRepository('AppBundle:Vote')->removeVotesByUserAndPost($this->getUser(), $vote->getPost());

                $em->persist($vote);
                $em->flush();

                $this->addFlash('notice', 'Votre vote à été pris en compte');

                return $this->redirectToRoute('index');
            }
        }

        $posts = $em->getRepository('AppBundle:Post')->findBy([], ['id' => 'DESC']);
        $posts = $this->countVotesForPosts($posts);

        return $this->render('AppBundle:Default:index.html.twig', array(
            'form' => !is_null($this->getUser()) ? $form->createView() : null,
            'voteForm' => $voteForm->createView(),
            'posts' => $posts
        ));
    }

    public function postAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->getPostForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setDatePost(new \DateTime('now'));

            $post->getImage()->upload();

            $em->persist($post);
            $em->flush();

            $this->addFlash('notice', 'Votre image est maintenant en ligne');
            return $this->redirectToRoute('index');
        }
    }

    private function getPostForm()
    {
        $post = new Post();
        $post->setUser($this->getUser());

        $form = $this->createForm(PostType::class, $post);

        return $form;
    }

    private function countVotesForPosts(array $posts)
    {
        $voteRepository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Vote');

        $nb = count($posts);
        for ($i = 0; $i < $nb; $i++) {
            $posts[$i]->upvotes = $voteRepository->countVotesByPost($posts[$i]);
            $posts[$i]->downvotes = $voteRepository->countVotesByPost($posts[$i], false);
        }

        return $posts;
    }

}
