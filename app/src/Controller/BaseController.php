<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\FormValidationService;

class BaseController extends AbstractController
{
    /** @var FormValidationService */
    private $formValidationService;

    public function __construct(FormValidationService $formValidationService)
    {
        $this->formValidationService = $formValidationService;
    }

    #[Route('/', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/form', name: 'form', methods: ['POST'], requirements: ['request' => '\s+'])]
    public function post(Request $request): JsonResponse
    {
        // Last digit of the PAN (card number) is checked using Luhn’s algorithm
        $response = $this->formValidationService->validateForm(json_decode($request->getContent(), true));
        
        return $this->json($response);
    }
}
