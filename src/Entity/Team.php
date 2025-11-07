<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $engineBrand = null;

    /**
     * @var Collection<int, Driver>
     */
    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Driver::class, cascade: ['persist'], orphanRemoval: false)]
    private Collection $drivers;

    public function __construct()
    {
        $this->drivers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEngineBrand(): ?string
    {
        return $this->engineBrand;
    }

    public function setEngineBrand(?string $engineBrand): self
    {
        $this->engineBrand = $engineBrand;
        return $this;
    }

    /**
     * @return Collection<int, Driver>
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): self
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers->add($driver);
            $driver->setTeam($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): self
    {
        if ($this->drivers->removeElement($driver) && $driver->getTeam() === $this) {
            $driver->setTeam(null);
        }

        return $this;
    }
}