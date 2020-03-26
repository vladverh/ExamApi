<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ApiEmployeeController extends AbstractController
{
    /**
     * @Route("/api/employee", name="api_employee")
     */
    public function index()
    {
        return $this->render('api_employee/index.html.twig', [
            'controller_name' => 'ApiEmployeeController',
        ]);
    }
}
