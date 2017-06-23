<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PostController extends Controller
{

    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->findOneBy([], ['datePost' => 'DESC']);

        $normalizer = new ObjectNormalizer();
//        $normalizer->setCircularReferenceHandler(function($object){
//            return 'lol';
//        });
        $normalizer->setIgnoredAttributes([
            'post',
            'password',
            'salt',
            'lastLogin',
            'file',
            'roles',
            'plainPassword'
            ]);

        $normalizer->setCallbacks([
            'datePost' => function(\DateTime $date){ return $date->format('d/m/Y H:i:s'); },
            'image' => function(Image $img) { return $img->getUploadDir() . '/' . $img->getUrl(); }
        ]);

        $encoder = new JsonEncoder();
        $serializer = new Serializer(array($normalizer), array($encoder));

        $jsonContent = $serializer->serialize($posts, 'json');
        $response = new JsonResponse();
        $response->setContent($jsonContent);

        return $response;
    }
}
