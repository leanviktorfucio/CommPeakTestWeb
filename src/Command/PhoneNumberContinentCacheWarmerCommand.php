<?php 

namespace App\Command;

use App\Service\CacheService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(
    name: 'app:warm-phone-number-continent-cache',
    description: 'Warm up cache for phone number by continent.',
)]
class PhoneNumberContinentCacheWarmerCommand extends Command {

    public function __construct(
        private KernelInterface $kernel,
        private CacheService $cacheService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        // $contents = file_get_contents();
        $filePath = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'countryInfo.txt';
        $fileContents = trim(file_get_contents(filename: $filePath));

        // a hashmap with continent code as key and list of phone number prefixes as value
        $phoneNumberPrefixesByContinentMap = [];

        // /**
        //  * I chose to this data structure for faster searching. O(1)
        //  * [
        //  *   [376] => 'EU',
        //  *   [971] => 'AS',
        //  *   [355] => 'EU'
        //  * ]
        //  * 
        //  * it could be a hashmap with continent code as key and list of phone number prefixes as value like this
        //  * [
        //  *   'EU' => [376, 355],
        //  *   'AS' => [971]
        //  * ]
        //  * but this will need nested loop O(n*m) to search for a certain continent code
        //  */
        // $phoneNumberPrefixToContinentCodeMap = [];

        // iterate through the whole countryInfo.txt
        foreach(explode(PHP_EOL, $fileContents) as $line) {

            // ignore commented out lines
            if (!str_starts_with(haystack: $line, needle: '#')) {

                // convert line to array using tab
                $lineAsArray = explode("\t", $line);

                $continentCode = $lineAsArray[CSVColumnIndex::CONTINENT_CODE];
                $phoneNumberPrefix = $lineAsArray[CSVColumnIndex::PHONE_NUMBER];
                
                // $phoneNumberPrefixToContinentCodeMap[$phoneNurmberPefix] = $continentCode;

                // get phonenumbers by continent array
                $phoneNumberPrefixesByContinent = [];
                if (isset($phoneNumberPrefixesByContinentMap[$continentCode])) {
                    $phoneNumberPrefixesByContinent = $phoneNumberPrefixesByContinentMap[$continentCode];
                }

                // append phone number to array only if not empty
                if (!empty($phoneNumberPrefix)) {
                    $phoneNumberPrefixesByContinent[] = $phoneNumberPrefix;
                }
                
                // save to hashmap
                $phoneNumberPrefixesByContinentMap[$continentCode] = $phoneNumberPrefixesByContinent;
            }
        }
        
        // save the hashmap to cache
        $this->cacheService->flushAll();
        $this->cacheService->set(key: 'continent-phonenumber-map', value: $phoneNumberPrefixesByContinentMap);

        return Command::SUCCESS;
    }
}

class CSVColumnIndex {
    const CONTINENT_CODE = 8;
    const PHONE_NUMBER = 12;
}