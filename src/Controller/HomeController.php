<?php

namespace App\Controller;

use App\Entity\CSVFileEntity;
use App\Form\CSVFileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
