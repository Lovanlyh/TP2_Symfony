<?php

namespace App\Service;

use App\Entity\Driver;
use App\Entity\Infraction;
use App\Entity\Team;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class InfractionService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function createInfraction(
        ?Driver $driver,
        ?Team $team,
        string $description,
        ?int $points,
        ?float $fine,
        string $raceName,
        ?DateTimeImmutable $occurredAt = null
    ): Infraction {
        if ($driver === null && $team === null) {
            throw new InvalidArgumentException('Une infraction doit cibler un pilote ou une écurie.');
        }

        if ($points === null && $fine === null) {
            throw new InvalidArgumentException('Une infraction doit contenir des points ou une amende.');
        }

        $infraction = new Infraction();
        $infraction->setDescription($description)
            ->setRaceName($raceName)
            ->setDriver($driver)
            ->setTeam($team)
            ->setOccurredAt($occurredAt ?? new DateTimeImmutable());

        $type = $points !== null ? Infraction::TYPE_PENALTY : Infraction::TYPE_FINE;
        $infraction->setType($type);

        if ($points !== null) {
            $infraction->setPoints($points);
        }

        if ($fine !== null) {
            $infraction->setAmount(number_format($fine, 2, '.', ''));
        }

        $this->applyInfraction($infraction);

        return $infraction;
    }

    private function applyInfraction(Infraction $infraction): void
    {
        $driver = $infraction->getDriver();

        if ($infraction->getType() === Infraction::TYPE_PENALTY && $driver) {
            $points = $infraction->getPoints() ?? 0;
            $newPoints = max(0, $driver->getLicensePoints() - $points);
            $driver->setLicensePoints($newPoints);

            if ($newPoints < 1) {
                $driver->setState('suspended');
            }

            $this->entityManager->persist($driver);
        }

        $this->entityManager->persist($infraction);
        $this->entityManager->flush();
    }
}   