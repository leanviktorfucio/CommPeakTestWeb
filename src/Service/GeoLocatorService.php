<?php

namespace App\Service;

use Exception;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeoLocatorService  {
    private $phoneNumberByContinentMap;

    public function __construct(
        private CacheService $cacheService,
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $httpClient
    ) {
        $this->phoneNumberByContinentMap = $this->cacheService->get('continent-phonenumber-map');
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
}