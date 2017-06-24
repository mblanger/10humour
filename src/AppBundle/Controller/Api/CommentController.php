<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Image;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CommentController extends Controller
{

    private $ignoredAttributes;

    public function __construct()
    {
        $this->ignoredAttributes = [
            'comments',
            'password',
            'salt',
            'lastLogin',
            'roles',
            'plainPassword',
            'post'
        ];
    }

    /**
     * @return JsonResponse
     */
    public function listForPostAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $comments = $em->getRepository('AppBundle:Comment')->findBy(['post' => $post], ['datePost' => 'DESC']);

        $jsonContent = $this->normalize($comments);
        $response = new JsonResponse();
        $response->setContent($jsonContent);

        return $response;
    }

    public function getAction(Post $post, Comment $comment){
        if($comment->getPost() !== $post){
            throw new NotFoundHttpException();
        }

        $jsonContent = $this->normalize($comment);
        $response = new JsonResponse();
        $response->setContent($jsonContent);

        return $response;
    }

    /**
     * @return string
     */
    private function normalize($content)
    {

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes($this->ignoredAttributes);

        $normalizer->setCallbacks([
            'datePost' => function(\DateTime $date){ return $date->format('d/m/Y H:i:s'); }
        ]);

        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $jsonContent = $serializer->serialize($content, 'json');

        return $jsonContent;
    }
}
