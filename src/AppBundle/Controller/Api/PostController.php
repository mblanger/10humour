<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Image;
use AppBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PostController extends Controller
{

    private $ignoredAttributes;

    public function __construct()
    {
        $this->ignoredAttributes = [
            'post',
            'password',
            'salt',
            'lastLogin',
            'file',
            'roles',
            'plainPassword'
        ];
    }

    /**
     * @return JsonResponse
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('AppBundle:Post')->findAllWithVotes();

        $jsonContent = $this->normalize($posts);
        $response = new JsonResponse();
        $response->setContent($jsonContent);

        return $response;
    }

    public function getAction(Post $post){
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->findWithVotes($post);
        $jsonContent = $this->normalize($post);
        $response = new JsonResponse();
        $response->setContent($jsonContent);

        return $response;
    }

    /**
     * @return string
     */
    private function normalize($content){

        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes($this->ignoredAttributes);

        $normalizer->setCallbacks([
            'datePost' => function(\DateTime $date){ return $date->format('d/m/Y H:i:s'); },
            'image' => function(Image $img) { return $img->getUploadDir() . '/' . $img->getUrl(); }
        ]);

        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $jsonContent = $serializer->serialize($content, 'json');

        return $jsonContent;
    }
}
