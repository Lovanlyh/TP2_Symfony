<?php

namespace App\Controller\Api;

use App\Entity\Team;
use App\Repository\DriverRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    #[Route('/api/teams/{id}/drivers', name: 'api_edit_team_drivers', methods: ['PUT'])]
    public function updateDrivers(
        Team $team,
        Request $request,
        DriverRepository $driverRepo,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload) || !isset($payload['drivers']) || !is_array($payload['drivers'])) {
            return new JsonResponse(['message' => 'Le tableau "drivers" est requis.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $drivers = [];
        foreach ($payload['drivers'] as $driverId) {
            $driver = $driverRepo->find((int) $driverId);
            if (!$driver) {
                return new JsonResponse(['message' => sprintf('Pilote %d introuvable.', $driverId)], JsonResponse::HTTP_NOT_FOUND);
            }
            $drivers[] = $driver;
        }

        foreach ($team->getDrivers() as $driver) {
            $team->removeDriver($driver);
        }

        foreach ($drivers as $driver) {
            $team->addDriver($driver);
        }

        $entityManager->persist($team);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $team->getId(),
            'name' => $team->getName(),
            'engineBrand' => $team->getEngineBrand(),
            'drivers' => array_map(static fn($driver) => [
                'id' => $driver->getId(),
                'firstName' => $driver->getFirstName(),
                'lastName' => $driver->getLastName(),
                'status' => $driver->getStatus(),
            ], $team->getDrivers()->toArray()),
        ]);
    }
}
