<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Table(name="MeterReads")
 * @ORM\Entity(repositoryClass="App\Repository\MeterReadsRepository")
 * @ApiResource()
 */
class MeterReads
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @ORM\ManyToOne(targetEntity="App\Entity\Meter", inversedBy="id")
     * @var string
     */
    private $meter;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $registerId;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $value;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $readType;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $readDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeter(): ?string
    {
        return $this->meter;
    }

    public function setMeter(string $meter): self
    {
        $this->meter = $meter;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRegisterId(): ?string
    {
        return $this->registerId;
    }

    public function setRegisterId(string $registerId): self
    {
        $this->registerId = $registerId;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getReadType(): ?string
    {
        return $this->readType;
    }

    public function setReadType(string $readType): self
    {
        $this->readType = $readType;

        return $this;
    }

    public function getReadDate(): ?\DateTimeInterface
    {
        return $this->readDate;
    }

    public function setReadDate(?\DateTimeInterface $readDate): self
    {
        $this->readDate = $readDate;

        return $this;
    }
}
