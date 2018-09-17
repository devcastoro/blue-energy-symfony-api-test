<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="meter")
 * @ORM\Entity(repositoryClass="App\Repository\MeterRepository")
 */
class Meter
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $mpxn;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $serialNumber;

    /**
     * @ORM\Column(type="string")
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="id")
     * @var string
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMpxn(): ?string
    {
        return $this->mpxn;
    }

    public function setMpxn(string $mpxn): self
    {
        $this->mpxn = $mpxn;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(string $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}