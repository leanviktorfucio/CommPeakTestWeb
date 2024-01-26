<?php

namespace App\Controller;

use App\Entity\CSVFileEntity;
use App\Form\CSVFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    #[Route('/', name: 'app_home',  methods: ['GET'])]
    public function index(Request $request): Response {
        // create a blank form
        $form = $this->createForm(
            type: CSVFileType::class,
            data: new CSVFileEntity(), 
            options: [
                'action' => '/upload',
                'method' => 'POST'
            ]
        );

        // render the view
        return $this->render(
            view: 'home/index.html.twig',
            parameters: ['form' => $form->createView()]
        );
    }

    #[Route('/upload', name: 'upload_csv',  methods: ['POST'])]
    public function uploadFile(Request $request): Response {
        $csvFileEntity = new CSVFileEntity();

        // create form from request
        $form = $this->createForm(type:CSVFileType::class, data: $csvFileEntity);
        $form->handleRequest(request: $request);

        // validate from
        $response = new JsonResponse();
        if ($form->isSubmitted() && $form->isValid()) {
            $response->setStatusCode(code: 200);
            $response->setData(data: []);
        } else {
            $formErrors = $this->getFormErrors(form: $form);
            $response->setStatusCode(code: 500);
            $response->setData(data: $formErrors);
        }

        return $response;
    }

    private function getFormErrors(Form $form): array
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }
}
