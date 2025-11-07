<?php

namespace App\DataFixtures;

use App\Entity\Infraction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InfractionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $infractions = [
            [
                'driver' => 'driver_0_1',
                'description' => 'Coupe une chicane',
                'points' => 3,
                'race' => 'Monaco 2025',
                'date' => '2025-05-26T14:30:00+00:00',
            ],
            [
                'team' => 'team_1',
                'description' => 'Unsafe release',
                'fine' => 7500,
                'race' => 'Silverstone 2025',
                'date' => '2025-07-14T16:00:00+00:00',
            ],
            [
                'driver' => 'driver_2_2',
                'description' => 'Collision au départ',
                'points' => 5,
                'race' => 'Spa 2025',
                'date' => '2025-08-31T13:10:00+00:00',
            ],
        ];

        foreach ($infractions as $data) {
            $infraction = new Infraction();
            $infraction->setDescription($data['description'])
                ->setRaceName($data['race'])
                ->setOccurredAt(new \DateTimeImmutable($data['date']))
                ->setCreatedAt(new \DateTimeImmutable($data['date']));

            if (isset($data['driver'])) {
                /** @var \App\Entity\Driver $driver */
                $driver = $this->getReference($data['driver']);
                $infraction->setType(Infraction::TYPE_PENALTY);
                $infraction->setDriver($driver);
                $infraction->setPoints($data['points'] ?? 0);
            }

            if (isset($data['team'])) {
                /** @var \App\Entity\Team $team */
                $team = $this->getReference($data['team']);
                $infraction->setTeam($team);
                if (!isset($data['driver'])) {
                    $infraction->setType(Infraction::TYPE_FINE);
                }
            }

            if (isset($data['fine'])) {
                $infraction->setType(Infraction::TYPE_FINE);
                $infraction->setAmount(number_format($data['fine'], 2, '.', ''));
            }

            $manager->persist($infraction);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AppFixtures::class,
        ];
    }
}
