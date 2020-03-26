<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Job;
use DateTime;
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

class ApiEmployeeController extends AbstractController
{
    public function __construct()
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    /**
     * @Route("/employees", name="api_employee_index", methods={"GET"})
     */
    public function index()
    {

        $employees = $this->getDoctrine()->getRepository(Employee::class)->findAll();

        $data = $this->serializer->normalize($employees, null, ['groups' => 'all_employees']);

        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/employees/{employee}", name="api_employee_show", methods={"GET"})
     */
    public function show(Request $request)
    {

        $employee = $this->getDoctrine()->getRepository(Employee::class)->find($request->get('employee'));

        $data = $this->serializer->normalize($employee, null, ['groups' => 'all_employees']);

        $jsonContent = $this->serializer->serialize($data, 'json');

        $response = new Response($jsonContent, 200);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/employees", name="api_employee_create", methods={"POST"})
     */
    public function create(Request $request)
    {


        $employee = new Employee;
        $employee->setFirstname($request->get('firstname'));
        $employee->setLastname($request->get('lastname'));
        $date = new DateTime($request->get('employement_date'));
        $employee->setEmployementDate($date);
        $job = $this->getDoctrine()->getRepository(Job::class)->find($request->get('job_id'));

        $employee->setJob($job);


        $manager = $this->getDoctrine()->getManager();
        $manager->persist($employee);
        $manager->flush();


        return new Response(null, 201);
    }

    /**
     * @Route("/employees/{employee}", name="employee_edit_patch", methods={"POST"})
     * @param Employee $employee
     */
    public function edit(request $request, Employee $employee)
    {
        $employee = $this->getDoctrine()->getRepository(Employee::class)->find($request->get('employee'));

        if (!empty($request->request->get('firstname'))) {
            $employee->setFirstname($request->get('firstname'));
        }
        if (!empty($request->request->get('lastname'))) {
            $employee->setLastname($request->get('lastname'));
        }
        if (!empty($request->request->get('employement_date'))) {
            $date = new DateTime($request->get('employement_date'));
            $employee->setEmployementDate($date);
        }
        if (!empty($request->request->get('lastname'))) {
            $job = $this->getDoctrine()->getRepository(Job::class)->find($request->get('job_id'));
            $employee->setJob($job);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($employee);
        $manager->flush();

        return new Response(null, 200);
    }
    /**
     * @Route("/employees/{employee}", name="api_employee_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Employee $employee)
    {
        $employee = $this->getDoctrine()->getRepository(Employee::class)->find($request->get('employee'));
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($employee);

        $manager->flush();

        return new Response(null, 204);
    }
}
