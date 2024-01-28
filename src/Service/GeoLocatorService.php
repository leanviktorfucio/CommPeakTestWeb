<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoLocatorService  {
    private $phoneNumberByContinentMap;
    
    private const CONTINENT_PHONENUMBER_MAP_CACHE_KEY = 'continent-phonenumber-map';

    public function __construct(
        private KernelInterface $kernel,
        private CacheService $cacheService,
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $httpClient
    ) {
        $this->phoneNumberByContinentMap = $this->cacheService->get(self::CONTINENT_PHONENUMBER_MAP_CACHE_KEY);
    }

    public function getContinentCodeByIP(string $ip): string {
        $cacheKey = 'gls.gcbipgl.' . $ip;
        $continentCode = $this->cacheService->get($cacheKey);
        if ($continentCode === false) {
            $geoLocationAPIKey = $this->parameterBag->get('app.ipgeolocation_api_key');
            $response = $this->httpClient->request(
                'GET',
                sprintf('https://api.ipgeolocation.io/ipgeo?apiKey=%s&ip=%s&fields=continent_code', $geoLocationAPIKey, $ip)
            );

            if ($response->getStatusCode() !== 200) {
                throw new Exception('There a problem fetching continent from api.ipgeolocation.io.');
            }

            $continentCode = json_decode($response->getContent())->continent_code;
            $this->cacheService->set(key: $cacheKey, value: $continentCode);
        }

        return $continentCode;
    }

    public function getContinentCodeByPhoneNumber(string $phoneNumber): string | null {
        $continentCode = null;

        if ($this->phoneNumberByContinentMap === false) {
            // warm the CONTINENT_PHONENUMBER_MAP_CACHE_KEY cache
            // we don't want to open the file everytime we need a continent by phone number
            $this->warmContinentPhonenumberMapCache();
            $this->phoneNumberByContinentMap = $this->cacheService->get(self::CONTINENT_PHONENUMBER_MAP_CACHE_KEY);
        }

        // iterate to get continent by id
        foreach($this->phoneNumberByContinentMap as $continentCodeKey => $phoneNumberPrefixes) {
            foreach($phoneNumberPrefixes as $phoneNumberPrefix) {
                if (str_starts_with($phoneNumber, $phoneNumberPrefix)) {
                    $continentCode = $continentCodeKey;
                    break 2;
                }
            }
        }

        return $continentCode;
    }

    private function warmContinentPhonenumberMapCache() {
        $filePath = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'countryInfo.txt';
        $fileContents = trim(file_get_contents(filename: $filePath));

        // a hashmap with continent code as key and list of phone number prefixes as value
        $phoneNumberPrefixesByContinentMap = [];

        /**
         * Data Structure
         * [
         *   'EU' => [376, 355],
         *   'AS' => [971],
         *   ...
         * ]
         *
         */
        // iterate through the whole countryInfo.txt
        foreach(explode("\n", $fileContents) as $line) {

            // ignore commented out lines
            if (!str_starts_with(haystack: $line, needle: '#')) {

                // convert line to array using tab
                $lineAsArray = explode("\t", $line);

                $continentCode = $lineAsArray[CountryInfoCSVColumnIndex::CONTINENT_CODE];
                $phoneNumberPrefix = $lineAsArray[CountryInfoCSVColumnIndex::PHONE_NUMBER];

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

        $this->cacheService->set(key: self::CONTINENT_PHONENUMBER_MAP_CACHE_KEY, value: $phoneNumberPrefixesByContinentMap);
    }
}

class CountryInfoCSVColumnIndex {
    const CONTINENT_CODE = 8;
    const PHONE_NUMBER = 12;
}