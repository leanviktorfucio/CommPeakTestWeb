<?php

namespace App\Entity;

use App\Repository\CallDataStatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallDataStatisticsRepository::class)]
class CallDataStatistics
{
    #[ORM\Id]
    #[ORM\Column(name: "customer_id", type: 'integer', nullable: false, unique: true)]
    private int $customerId;
    
    #[ORM\Column(name: "number_of_calls_within_same_continent", type: 'integer', nullable: false)]
    private $numberOfCallsWithinSameContinent;
    
    #[ORM\Column(name: "duration_of_calls_within_same_continent", type: 'integer', nullable: false)]
    private $durationOfCallsWithinSameContinent;
    
    #[ORM\Column(name: "total_number_of_calls", type: 'integer', nullable: false)]
    private $totalNumberOfCalls;
    
    #[ORM\Column(name: "total_duration_of_calls", type: 'integer', nullable: false)]
    private $totalDurationOfCalls;

    public function getCustomerId(): int {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void {
        $this->customerId = $customerId;
    }

    public function getNumberOfCallsWithinSameContinent(): int {
        return $this->numberOfCallsWithinSameContinent;
    }

    public function setNumberOfCallsWithinSameContinent(int $numberOfCallsWithinSameContinent): void {
        $this->numberOfCallsWithinSameContinent = $numberOfCallsWithinSameContinent;
    }

    public function getDurationOfCallsWithinSameContinent(): int {
        return $this->durationOfCallsWithinSameContinent;
    }

    public function setDurationOfCallsWithinSameContinent(int $durationOfCallsWithinSameContinent): void {
        $this->durationOfCallsWithinSameContinent = $durationOfCallsWithinSameContinent;
    }

    public function getTotalNumberOfCalls(): int {
        return $this->totalNumberOfCalls;
    }

    public function setTotalNumberOfCalls(int $totalNumberOfCalls): void {
        $this->totalNumberOfCalls = $totalNumberOfCalls;
    }

    public function getTotalDurationOfCalls(): int {
        return $this->totalDurationOfCalls;
    }

    public function setTotalDurationOfCalls(int $totalDurationOfCalls): void {
        $this->totalDurationOfCalls = $totalDurationOfCalls;
    }

    // this will create a new CallDataStatistics object with default values
    public static function getInstance($customerId): CallDataStatistics {
        $CallDataStatistics = new CallDataStatistics();
        $CallDataStatistics->setCustomerId($customerId);
        $CallDataStatistics->setNumberOfCallsWithinSameContinent(0);
        $CallDataStatistics->setDurationOfCallsWithinSameContinent(0);
        $CallDataStatistics->setTotalNumberOfCalls(0);
        $CallDataStatistics->setTotalDurationOfCalls(0);

        return $CallDataStatistics;
    }

    public function toCSV(): string {
        $csvData = [
            $this->customerId,
            $this->numberOfCallsWithinSameContinent,
            $this->durationOfCallsWithinSameContinent,
            $this->totalNumberOfCalls,
            $this->totalDurationOfCalls,
        ];

        return implode(separator: ',', array: $csvData);
    }

    public function toArray() {
        return [
            'customerId' => $this->customerId,
            'numberOfCallsWithinSameContinent' => $this->numberOfCallsWithinSameContinent,
            'durationOfCallsWithinSameContinent' => $this->durationOfCallsWithinSameContinent,
            'totalNumberOfCalls' => $this->totalNumberOfCalls,
            'totalDurationOfCalls' => $this->totalDurationOfCalls
        ];
    }
}
