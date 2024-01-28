<?php

namespace App\Service;

use App\Entity\CallData;
use App\Entity\CallDataStatistics;
use App\Repository\CallDataRepository;
use App\Repository\CallDataStatisticsRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CallDataService  {
    public function __construct(
        private CallDataRepository $callDataRepository,
        private CallDataStatisticsRepository $callDataStatisticsRepository,
        private CallDataStatisticsService $callDataStatisticsService,
        private GeoLocatorService $geoLocatorService
    ) {
    }

    public function load(array $csvFiles): void {
        // Step 1: load csv files to CallData table
        // This will prevent duplicate data by customerId and datetime
        try {
            foreach($csvFiles as $csvFile) {
                /** @var UploadedFile $csvFile */
                $this->callDataRepository->importCSV(csvFilePath: $csvFile->getPathname());
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to process the file [' . $csvFile->getClientOriginalName() . ']: ' . $e->getMessage());
        }
        
        // Step 2: get all CallData and process them to make new set of CallDataStatistic data
        $callDataList = $this->callDataRepository->findAll();

        // this will serve as a hashmap for all CallDataStatistic with customerId as key
        $callDataStatisticsMap = [];

        // loop through existing list of call data to generate call data statistics
        foreach($callDataList as $callData) {
            $customerId = $callData->getCustomerId();

            // create new one if it doesn't exist yet
            if (isset($callDataStatisticsMap[$customerId])) {
                $callDataStat = $callDataStatisticsMap[$customerId];
            } else {
                $callDataStat = CallDataStatistics::getInstance(customerId: $customerId);
            }

            // process the callData and set new data to $callDataStat
            $this->processCallDataStatisticsFromCallData(callDataStatistics: $callDataStat, callData: $callData);

            // update the hashmap
            $callDataStatisticsMap[$customerId] = $callDataStat;
        }

        // Step 3: save processed statistics data
        $this->callDataStatisticsService->importMapToTable(callDataStatisticsMap: $callDataStatisticsMap);
    }

    private function processCallDataStatisticsFromCallData(CallDataStatistics &$callDataStatistics, CallData $callData) {
        // get current data
        $numberOfCallsWithinSameContinent = $callDataStatistics->getNumberOfCallsWithinSameContinent();
        $durationOfCallsWithinSameContinent = $callDataStatistics->getDurationOfCallsWithinSameContinent();
        $totalNumberOfCalls = $callDataStatistics->getTotalNumberOfCalls();
        $totalDurationOfCalls = $callDataStatistics->getTotalDurationOfCalls();

        // check if same continent to calculate new data for related fields
        $isSameContinent = $this->isIpAndPhoneNumberSameContinent($callData->getIp(), $callData->getPhoneNumber());
        if ($isSameContinent) {
            $numberOfCallsWithinSameContinent++;
            $durationOfCallsWithinSameContinent += $callData->getDuration();
        }
        $totalNumberOfCalls++;
        $totalDurationOfCalls += $callData->getDuration();

        // set new data
        $callDataStatistics->setNumberOfCallsWithinSameContinent($numberOfCallsWithinSameContinent);
        $callDataStatistics->setDurationOfCallsWithinSameContinent($durationOfCallsWithinSameContinent);
        $callDataStatistics->setTotalNumberOfCalls($totalNumberOfCalls);
        $callDataStatistics->setTotalDurationOfCalls($totalDurationOfCalls);
    }

    private function isIpAndPhoneNumberSameContinent(string $ip, string $phoneNumber): bool {
        $phoneNumberContinent = $this->geoLocatorService->getContinentCodeByPhoneNumber($phoneNumber);
        $ipContinent = $this->geoLocatorService->getContinentCodeByIP($ip);

        return $phoneNumberContinent === $ipContinent;
    }
}