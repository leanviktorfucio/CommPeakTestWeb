<?php

namespace App\Entity;

use App\Repository\CallDataRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CallDataRepository::class)]
#[ORM\UniqueConstraint(
    columns: ['customer_id', 'datetime']
)]
class CallData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(name: "customer_id", type: 'integer', nullable: false)]
    private int $customerId;

    #[ORM\Column(name: "datetime", type: 'datetime', nullable: false)]
    private DateTime $dateTime;

    #[ORM\Column(name: "duration", type: 'integer', nullable: false)]
    private int $duration;

    #[ORM\Column(name: "phone_number", type: 'string', length: 20, nullable: false)]
    private string $phoneNumber;

    #[ORM\Column(name: "ip", type: 'string', length: 20,  nullable: false)]
    private string $ip;

    public function getId(): int {
        return $this->id;
    }

    public function getCustomerId(): int {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): void {
        $this->customerId = $customerId;
    }

    public function getDateTime(): DateTime {
        return $this->dateTime;
    }

    public function setDateTime(DateTime $dateTime): void {
        $this->dateTime = $dateTime;
    }

    public function getDuration(): int {
        return $this->duration;
    }

    public function setDuration(int $duration): void {
        $this->duration = $duration;
    }

    public function getPhoneNumber(): string {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void {
        $this->phoneNumber = $phoneNumber;
    }

    public function getIp(): string {
        return $this->ip;
    }

    public function setIp(string $ip): void {
        $this->ip = $ip;
    }
}
