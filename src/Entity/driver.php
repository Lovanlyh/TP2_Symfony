<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Driver
{
#[ORM\Id, ORM\GeneratedValue, ORM\Column(type:'integer')]
private ?int $id = null;


#[ORM\Column(type:'string')]
private string $firstName;


#[ORM\Column(type:'string')]
private string $lastName;


#[ORM\Column(type:'integer')]
private int $licensePoints = 12;


#[ORM\Column(type:'datetime')]
private \DateTimeInterface $startedAt;


// "titulaires" or "reservistes"
#[ORM\Column(type:'string')]
private string $status = 'titulaires';


#[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'drivers')]
#[ORM\JoinColumn(nullable:true, onDelete:'SET NULL')]
private ?Team $team = null;


#[ORM\Column(type:'string')]
private string $state = 'active'; // active|suspended


// getters & setters...
public function getId(): ?int { return $this->id; }
public function getFirstName(): string { return $this->firstName; }
public function setFirstName(string $n): self { $this->firstName = $n; return $this; }
public function getLastName(): string { return $this->lastName; }
public function setLastName(string $n): self { $this->lastName = $n; return $this; }
public function getLicensePoints(): int { return $this->licensePoints; }
public function setLicensePoints(int $p): self { $this->licensePoints = $p; return $this; }
public function getStartedAt(): \DateTimeInterface { return $this->startedAt; }
public function setStartedAt(\DateTimeInterface $d): self { $this->startedAt = $d; return $this; }
public function getStatus(): string { return $this->status; }
public function setStatus(string $s): self { $this->status = $s; return $this; }
public function getTeam(): ?Team { return $this->team; }
public function setTeam(?Team $t): self { $this->team = $t; return $this; }
public function getState(): string { return $this->state; }
public function setState(string $s): self { $this->state = $s; return $this; }
}