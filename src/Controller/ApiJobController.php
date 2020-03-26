<?php

namespace App\Controller;

use App\Entity\Job;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiJobController extends AbstractController
{
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/api/jobs", name="api_job", methods={"GET"})
     */
    public function index()
    {

        $jobs = $this->getDoctrine()->getRepository(Job::class)->findAll();

        $data = $this->serializer->normalize($jobs, null, ['groups' => 'all_jobs']);

        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/api/jobs/{job}", name="api_job_show", methods={"GET"})
     */
    public function show(Request $request)
    {

        $job = $this->getDoctrine()->getRepository(Job::class)->find($request->get('job'));

        $data = $this->serializer->normalize($job, null, ['groups' => 'all_jobs']);

        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    /**
     * @Route("/api/jobs", name="api_job_create", methods={"POST"})
     */
    public function create(Request $request)
    {


        $job = new Job;
        $job->setTitle($request->get('title'));




        $manager = $this->getDoctrine()->getManager();
        $manager->persist($job);
        $manager->flush();


        return new Response(null, 201);
    }

    /**
     * @Route("/api/jobs/{job}", name="job_edit_patch", methods={"POST"})
     * @param Job $job
     */
    public function edit(request $request, Job $job)
    {
        $job = $this->getDoctrine()->getRepository(Job::class)->find($request->get('job'));

        if (!empty($request->request->get('title'))) {
            $job->setTitle($request->get('title'));
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($job);
        $manager->flush();

        return new Response(null, 200);
    }
    /**
     * @Route("api/jobs/{job}", name="api_job_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Job $job)
    {
        $job = $this->getDoctrine()->getRepository(Job::class)->find($request->get('job'));
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($job);

        $manager->flush();

        return new Response(null, 204);
    }
}
