<?php

namespace App\Service;

use App\Entity\CallDataStatistics;
use App\Repository\CallDataRepository;
use Symfony\Component\HttpFoundation\File\File;

class CallDataService  {
    public function __construct(
        private CallDataRepository $callDataRepository,
        private GeoLocatorService $geoLocatorService
    ) {
    }

    public function load(array $csvFiles): void {
        try {
             // Step 1: load csv files to CallData table
            // This will prevent duplicate data by customerId and datetime
            foreach($csvFiles as $csvFile) {
                /** @var File $csvFile */
                $this->callDataRepository->importCSV(csvFile: $csvFile);
            }

            // Step 2: get all CallData and process them to make CallDataStatistic

            // Step 3: save processed statistics data

            // this will serve as a hashmap for all CallDataStatistic with customerId as key

            
        } catch (\Exception $e) {
            throw new \Exception('Failed to process the file [' . $csvFile->getClientOriginalName() . ']: ' . $e->getMessage());
        }

        // $this->dataLoaderService->loadByCSV('');
    }

    private function doLoad(File $file): void {
        // $file = fopen(filename: $filePath, mode: 'r');

        try {
            // Step 1: load csv files to CallData table
            // This will prevent duplicate data by customerId and datetime
            

            // Step 2: get all CallData and process them to make CallDataStatistic

            // Step 3: save processed statistics data

            // this will serve as a hashmap for all CallDataStatistic with customerId as key
            $callDataStatisticsMap = [];

            // // Read the file line by line
            // while (($line = fgets(stream: $file)) !== false) {
            //     $lineData = $this->parseLine(line: $line);
            //     $customerId = $lineData['customerId'];

            //     // get the existing callData object from the array
            //     // create new one if it doesn't exist yet
            //     if (isset($callDataStatisticsMap[$customerId])) {
            //         $callDataStatistics = $callDataStatisticsMap[$customerId];
            //     } else {
            //         $callDataStatistics = CallDataStatistics::getInstance($customerId);
            //     }

            //     // process the $lineData and set new data to $callDataStatistics
            //     $this->processCallDataStatisticsFromLineData(callDataStatistics: $callDataStatistics, lineData: $lineData);

            //     // update the array
            //     $callDataStatisticsMap[$customerId] = $callDataStatistics; 
            // }
        } finally {
            // Close the file handle
            // fclose($file);
        }

        // after all csv files are done, generate a csv file for loading to the database
        $tempCSVFilePath = $this->generateCsvFromMap(callDataStatisticsMap: $callDataStatisticsMap);
        var_dump($tempCSVFilePath);
        // $this->dataLoaderService->loadByCSV('');
    }

    private function parseLine(string $line) : array {
        $lineData = explode(separator: ',', string: trim($line));

        return [
            'customerId' => (int) $lineData[CSVColumnIndex::CUSTOMER_ID],
            'dateTime' => $lineData[CSVColumnIndex::DATETIME],
            'duration' => (int) $lineData[CSVColumnIndex::DURATION],
            'phoneNumber' => $lineData[CSVColumnIndex::PHONE_NUMBER],
            'ip' => $lineData[CSVColumnIndex::IP]
        ];
    }

    private function processCallDataStatisticsFromLineData(CallDataStatistics &$callDataStatistics, array $lineData) {
        $numberOfCallsWithinSameContinent = $callDataStatistics->getNumberOfCallsWithinSameContinent();
        $durationOfCallsWithinSameContinent = $callDataStatistics->getDurationOfCallsWithinSameContinent();
        $totalNumberOfCalls = $callDataStatistics->getTotalNumberOfCalls() ;
        $totalDurationOfCalls = $callDataStatistics->getTotalDurationOfCalls() + $lineData['duration'];

        $isSameContinent = $this->isIpAndPhoneNumberSameContinent($lineData['ip'], $lineData['phoneNumber']);
        if ($isSameContinent) {
            $numberOfCallsWithinSameContinent++;
            $durationOfCallsWithinSameContinent += $lineData['duration'];
        }

        $totalNumberOfCalls++;
        $totalDurationOfCalls += $lineData['duration'];

        $callDataStatistics->setNumberOfCallsWithinSameContinent($numberOfCallsWithinSameContinent);
        $callDataStatistics->setDurationOfCallsWithinSameContinent($durationOfCallsWithinSameContinent);
        $callDataStatistics->setTotalNumberOfCalls($totalNumberOfCalls);
        $callDataStatistics->setTotalDurationOfCalls($totalDurationOfCalls);
    }

    private function generateCsvFromMap(array $callDataStatisticsMap): string {
        // create temporary csv file
        $tempCSVFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR  . 'dump_' . uniqid() . '.csv';

        $file = null;

        try {
            $file = fopen($tempCSVFilePath, 'w');

            foreach($callDataStatisticsMap as $callDataStatistics) {
                /** @var CallDataStatistic $callDataStatistics */
                fwrite($file, $callDataStatistics->toCSV() . PHP_EOL);
            }

        } finally {
            fclose($file);
        }

        return $tempCSVFilePath;
    }

    private function isIpAndPhoneNumberSameContinent(string $ip, string $phoneNumber): bool {
        $phoneNumberContinent = $this->geoLocatorService->getContinentCodeByPhoneNumber($phoneNumber);
        $ipContinent = $this->geoLocatorService->getContinentCodeByIP($ip);

        return $phoneNumberContinent === $ipContinent;
    }
}

class CSVColumnIndex {
    const CUSTOMER_ID = 0;
    const DATETIME = 1;
    const DURATION = 2;
    const PHONE_NUMBER = 3;
    const IP = 4;
}