<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Entity\Vote;
use AppBundle\Form\PostType;
use AppBundle\Form\VoteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $posts = $em->getRepository('AppBundle:Post')->findAllWithVotes();

        return $this->render('AppBundle:Default:index.html.twig', array(
            'form' => !is_null($this->getUser()) ? $this->getPostForm()->createView() : null,
            'voteForm' => $this->getVoteForm()->createView(),
            'posts' => $posts
        ));
    }

    public function voteAction(Request $request){
        if ($this->isGranted('ROLE_USER')) {
            $em = $this->getDoctrine()->getManager();

            $voteForm = $this->getVoteForm();
            $voteForm->handleRequest($request);

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
    }

    private function getVoteForm(){
        $vote = new Vote();
        $vote->setUser($this->getUser());
        $voteForm = $this->createForm(VoteType::class, $vote);
        return $voteForm;
    }

    public function postAction(Request $request)
    {
        if($this->isGranted('ROLE_USER')) {

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
            }else{
                $errors = $this->get('app.formerrorsformatter')->getErrorMessages($form);
                $this->addFlash('errors', $errors);
            }
            return $this->redirectToRoute('index');
        }else{
            throw new AccessDeniedException();
        }
    }

    private function getPostForm()
    {
        $post = new Post();
        $post->setUser($this->getUser());

        $form = $this->createForm(PostType::class, $post);

        return $form;
    }

}
