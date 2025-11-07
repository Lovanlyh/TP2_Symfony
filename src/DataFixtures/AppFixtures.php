<?php
namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Team;
use App\Entity\Driver;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
public function __construct(private UserPasswordHasherInterface $hasher) {}


public function load(ObjectManager $manager)
{
$teams = [];
$teamNames = ['Ferrari', 'Alpine', 'Mclaren', 'Mercedes', 'Haas', 'Aston Martin', 'Williams', 'Sauber', 'Red Bull', 'Racing Bulls'];
$engines = ['Ferrari', 'Renault','Mercedes', 'Mercedes', 'Ferrari', 'Mercedes', 'Mercedes','Ferrari', 'Honda RBPT', 'Honda RBPT'];


foreach($teamNames as $i => $name){
$team = new Team();
$team->setName($name)->setEngineBrand($engines[$i] ?? 'Generic');


for($j=1;$j<=30;$j++){
$driver = new Driver();
$driver->setFirstName("Pilot{$i}{$j}")
->setLastName("Surname{$i}{$j}")
->setStartedAt(new \DateTimeImmutable(sprintf('201%d-03-01', $i)))
->setLicensePoints(12)
->setStatus($j===3 ? 'reservistes' : 'titulaires');
$team->addDriver($driver);
$manager->persist($driver);
}
$manager->persist($team);
$teams[] = $team;
}


// Create an admin user
$user = new User();
$user->setEmail('admin@example.com');
$user->setRoles(['ROLE_ADMIN']);
$user->setPassword($this->hasher->hashPassword($user, 'adminpass'));
$manager->persist($user);


$manager->flush();
}
}