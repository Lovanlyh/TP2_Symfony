<?php
namespace App\Service;


use App\Entity\Infraction;
use App\Entity\Driver;
use Doctrine\ORM\EntityManagerInterface;


class InfractionService
{
public function __construct(private EntityManagerInterface $em) {}


/**
* Applique l'infraction : persist + effets (points, suspension)
*/
public function applyInfraction(Infraction $infraction): void
{
$driver = $infraction->getDriver();


if($infraction->getType() === Infraction::TYPE_PENALTY && $driver){
$points = $infraction->getPoints() ?? 0;
$new = max(0, $driver->getLicensePoints() - $points);
$driver->setLicensePoints($new);


if($new < 1){
$driver->setState('suspended');
}


$this->em->persist($driver);
}


$this->em->persist($infraction);
$this->em->flush();
}
}   