<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiController extends Controller
{
    private $normalizers;
    private $encoders;
    private $serializer;
    private $response;
    private $responseBody;

    public function __construct()
    {
        $this->encoders = array(new JsonEncoder());
        $this->normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($this->normalizers, $this->encoders);
        $this->response = new Response();
        $this->response->headers->set('Content', 'json/application');
        $this->responseBody = array(
            'success' => true,
            'message' => null,
            'data' => null
        );
    }

    public function indexAction($entity)
    {
        $em = $this->getDoctrine()->getManager();
        $object = $em->getRepository('AppBundle:'.ucfirst($entity))->findAll();

        if (!$object) $this->responseBody['success'] = false;
        $this->responseBody['data'] = $object;
        $this->response->setContent($this->serializer->serialize($this->responseBody, 'json'));

        return $this->response;
    }

    public function commentsAction($appName)
    {
        $em = $this->getDoctrine()->getManager();
        $source = $em->getRepository('AppBundle:Application')->findOneBy(array('name' => $appName));
        $object = $em->getRepository('AppBundle:Commentary')->findBy(array('application' => $source));

        if (!$object) $this->responseBody['success'] = false;
        $this->responseBody['data'] = $object;
        $this->response->setContent($this->serializer->serialize($this->responseBody, 'json'));

        return $this->response;
    }
}
