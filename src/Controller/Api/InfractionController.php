<?php

namespace App\Controller\Api;

use App\Entity\Infraction;
use App\Repository\DriverRepository;
use App\Repository\InfractionRepository;
use App\Repository\TeamRepository;
use App\Service\InfractionService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class InfractionController extends AbstractController
{
    #[Route('/api/infractions', name: 'api_add_infraction', methods: ['POST'])]
    public function addInfraction(
        Request $request,
        DriverRepository $driverRepo,
        TeamRepository $teamRepo,
        InfractionService $infractionService
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return new JsonResponse(['message' => 'JSON invalide'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $description = $data['description'] ?? null;
        $race = $data['race'] ?? null;

        if (!$description || !$race) {
            return new JsonResponse(['message' => 'description et course requis'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $driver = isset($data['driver']) ? $driverRepo->find((int) $data['driver']) : null;
        $team = isset($data['team']) ? $teamRepo->find((int) $data['team']) : null;
        if (isset($data['team']) && !$team) {
            return new JsonResponse(['message' => sprintf('Écurie %d introuvable.', (int) $data['team'])], JsonResponse::HTTP_NOT_FOUND);
        }
        $points = isset($data['points']) ? (int) $data['points'] : null;
        $fine = isset($data['fine']) ? (float) $data['fine'] : null;
        $occurred = null;
        if (isset($data['date'])) {
            try {
                $occurred = new DateTimeImmutable($data['date']);
            } catch (\Throwable) {
                return new JsonResponse(['message' => 'Format de date invalide.'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        try {
            $infraction = $infractionService->createInfraction(
                $driver,
                $team,
                $description,
                $points,
                $fine,
                $race,
                $occurred
            );
        } catch (\Throwable $e) {
            return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse($this->formatInfraction($infraction), JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/infractions', name: 'api_list_infractions', methods: ['GET'])]
    public function listInfractions(
        Request $request,
        InfractionRepository $repository
    ): JsonResponse {
        $teamId = $request->query->get('team');
        $driverId = $request->query->get('driver');
        $dateParam = $request->query->get('date');

        $date = null;
        if ($dateParam) {
            try {
                $date = new DateTimeImmutable($dateParam);
            } catch (\Throwable) {
                return new JsonResponse(['message' => 'Format de date invalide (attendu YYYY-MM-DD).'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $infractions = $repository->findByFilters(
            $teamId !== null ? (int) $teamId : null,
            $driverId !== null ? (int) $driverId : null,
            $date
        );

        return new JsonResponse(array_map([$this, 'formatInfraction'], $infractions));
    }

    private function formatInfraction(\App\Entity\Infraction $infraction): array
    {
        return [
            'id' => $infraction->getId(),
            'type' => $infraction->getType(),
            'description' => $infraction->getDescription(),
            'race' => $infraction->getRaceName(),
            'occurredAt' => $infraction->getOccurredAt()->format(DATE_ATOM),
            'points' => $infraction->getPoints(),
            'fine' => $infraction->getAmount(),
            'driver' => $infraction->getDriver()?->getId(),
            'team' => $infraction->getTeam()?->getId(),
        ];
    }
}

