<?php

namespace App\Controller;

use App\Entity\CSVFileEntity;
use App\Form\CSVFileType;
use App\Service\CallDataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileUploadController extends AbstractController {

    public function __construct(
        private CallDataService $callDataService
    ) {
        
    }

    #[Route('/upload', name: 'upload_csv',  methods: ['POST'])]
    public function uploadFile(Request $request): Response {
        $csvFileEntity = new CSVFileEntity();

        // create form from request
        $form = $this->createForm(type: CSVFileType::class, data: $csvFileEntity);
        $form->handleRequest(request: $request);

        // validate from
        $response = new JsonResponse();
        if ($form->isSubmitted() && $form->isValid()) {

            $csvFiles = $form->get('files')->getData();
            // foreach($csvFiles as $csvFile) {
            //     /** @var UploadedFile $csvFile */
            //     $this->callDataService->load(filePath: $csvFile->getPathname());
            // }

            $this->callDataService->load(csvFiles: $csvFiles);

            $response->setStatusCode(code: 200);
            $response->setData(data: 'Success!');
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