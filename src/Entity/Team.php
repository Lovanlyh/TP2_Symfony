<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity]
class Team
{
#[ORM\Id, ORM\GeneratedValue, ORM\Column(type:'integer')]
private ?int $id = null;


#[ORM\Column(type:'string')]
private string $name;


// One engine (brand name only)
#[ORM\Column(type:'string', nullable:true)]
private ?string $engineBrand = null;


#[ORM\OneToMany(mappedBy:'team', targetEntity: Driver::class, cascade:['persist','remove'])]
private Collection $drivers;


public function __construct(){ $this->drivers = new ArrayCollection(); }


// getters & setters...
public function getId(): ?int { return $this->id; }
public function getName(): string { return $this->name; }
public function setName(string $name): self { $this->name = $name; return $this; }
public function getEngineBrand(): ?string { return $this->engineBrand; }
public function setEngineBrand(?string $b): self { $this->engineBrand = $b; return $this; }
public function getDrivers(): Collection { return $this->drivers; }
public function addDriver(Driver $d): self { if(!$this->drivers->contains($d)){ $this->drivers->add($d); $d->setTeam($this); } return $this; }
public function removeDriver(Driver $d): self { if($this->drivers->removeElement($d)){ $d->setTeam(null); } return $this; }
}