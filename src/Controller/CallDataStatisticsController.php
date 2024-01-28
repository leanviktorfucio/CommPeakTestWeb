<?php

namespace App\Controller;

use App\Service\CallDataStatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CallDataStatisticsController extends AbstractController {
    public function __construct(
        private CallDataStatisticsService $callDataStatisticsService
    ) {}

    #[Route('/statistics', name: 'app_call_data')]
    public function index(): Response
    {
        $callDataStatistics = $this->callDataStatisticsService->getAll();
        
        $callDataStatisticsArray = array_map(callback: function($callDataStat) {
            /** @var CallDataStatistics $callDataStat */
            return $callDataStat->toArray();
        }, array: $callDataStatistics);

        return new JsonResponse($callDataStatisticsArray);
    }
}
