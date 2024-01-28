<?php

namespace App\Service;

use App\Repository\CallDataStatisticsRepository;

class CallDataStatisticsService  {
    public function __construct(
        private CallDataStatisticsRepository $callDataStatisticsRepository
    ) {}

    public function getAll(): array {
        return $this->callDataStatisticsRepository->findBy([], ['customerId' => 'ASC']);
    }

    public function importMapToTable(array $callDataStatisticsMap) {
        // generate csv from hashmap
        $tempCSVFilePath = $this->generateCsvFromMap(callDataStatisticsMap: $callDataStatisticsMap);

        // import csv to table
        $this->callDataStatisticsRepository->importCSV(csvFilePath: $tempCSVFilePath);
    }

    private function generateCsvFromMap(array $callDataStatisticsMap): string {
        // create temporary csv file
        $tempCSVFilePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR  . 'dump_' . uniqid() . '.csv';

        $file = null;

        try {
            // use fopen instead of file_get_contents() which may cause max memory error when uploading big csv
            $file = fopen($tempCSVFilePath, 'w');

            foreach($callDataStatisticsMap as $callDataStatistics) {
                /** @var CallDataStatistic $callDataStatistics */
                fwrite($file, $callDataStatistics->toCSV() . "\n");
            }

        } finally {
            fclose($file);
        }

        return $tempCSVFilePath;
    }
}