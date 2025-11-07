<?php

namespace App\DataFixtures;

use App\Entity\Driver;
use App\Entity\Team;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $teamsData = [
            ['name' => 'Ferrari', 'engine' => 'Ferrari'],
            ['name' => 'Mercedes AMG', 'engine' => 'Mercedes'],
            ['name' => 'Red Bull Racing', 'engine' => 'Honda RBPT'],
        ];

        foreach ($teamsData as $index => $data) {
            $team = (new Team())
                ->setName($data['name'])
                ->setEngineBrand($data['engine']);

            $this->addReference('team_'.$index, $team);

            for ($i = 1; $i <= 3; $i++) {
                $driver = (new Driver())
                    ->setFirstName(sprintf('Driver%d%d', $index, $i))
                    ->setLastName(sprintf('Lastname%d%d', $index, $i))
                    ->setStartedAt(new \DateTimeImmutable(sprintf('201%d-03-01', $index + 1)))
                    ->setStatus($i === 3 ? 'reservistes' : 'titulaires');

                $team->addDriver($driver);
                $this->addReference(sprintf('driver_%d_%d', $index, $i), $driver);
                $manager->persist($driver);
            }

            $manager->persist($team);
        }

        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'adminpass'));

        $manager->persist($admin);
        $manager->flush();
    }
}