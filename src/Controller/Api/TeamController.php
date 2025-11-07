<?php
namespace App\Controller\Api;

use App\Entity\Team;
use App\Entity\Driver;
use App\Repository\TeamRepository;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TeamController extends AbstractController
{
    #[Route('/api/teams/{id}/drivers', name: 'api_edit_team_drivers', methods: ['PUT'])]
    public function updateDrivers(
        Team $team,
        Request $request,
        DriverRepository $driverRepo,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $driverIds = $data['drivers'] ?? [];

        foreach ($team->getDrivers() as $driver) {
            $team->removeDriver($driver);
        }

        foreach ($driverIds as $id) {
            $driver = $driverRepo->find($id);
            if ($driver) {
                $team->addDriver($driver);
            }
        }

        $em->persist($team);
        $em->flush();

        return new JsonResponse($serializer->serialize($team, 'json'), 200, [], true);
    }
}
