<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Infraction
{
public const TYPE_PENALTY = 'penalty';
public const TYPE_FINE = 'fine';


#[ORM\Id, ORM\GeneratedValue, ORM\Column(type:'integer')]
private ?int $id = null;


#[ORM\Column(type:'string')]
private string $type; // penalty|fine


#[ORM\Column(type:'integer', nullable:true)]
private ?int $points = null; // for penalty


#[ORM\Column(type:'decimal', precision:10, scale:2, nullable:true)]
private ?string $amount = null; // for fine (as string/decimal)


#[ORM\Column(type:'text')]
private string $description;


#[ORM\Column(type:'string')]
private string $raceName;


#[ORM\Column(type:'datetime')]
private \DateTimeInterface $occurredAt;


#[ORM\ManyToOne(targetEntity: Driver::class)]
private ?Driver $driver = null;


#[ORM\ManyToOne(targetEntity: Team::class)]
private ?Team $team = null;


#[ORM\Column(type:'datetime')]
private \DateTimeInterface $createdAt;


public function __construct(){ $this->createdAt = new \DateTimeImmutable(); }


// getters & setters...
}