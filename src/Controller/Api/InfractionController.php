<?php

namespace App\Controller\Api;

use App\Repository\DriverRepository;
use App\Repository\InfractionRepository;
use App\Repository\TeamRepository;
use App\Service\InfractionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class InfractionController extends AbstractController
{
    #[Route('/api/infractions', name: 'api_add_infraction', methods: ['POST'])]
    public function addInfraction(
        Request $request,
        DriverRepository $driverRepo,
        TeamRepository $teamRepo,
        InfractionService $infractionService,
        SerializerInterface $serializer
    ): JsonResponse {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $pilotId = $data['driver'] ?? null;
        $teamId = $data['team'] ?? null;
        $desc = $data['description'] ?? null;
        $points = $data['points'] ?? null;
        $fine = $data['fine'] ?? null;
        $race = $data['race'] ?? null;

        $pilot = $pilotId ? $driverRepo->find($pilotId) : null;
        $team = $teamId ? $teamRepo->find($teamId) : null;

        $inf = $infractionService->createInfraction($pilot, $team, $desc, $points, $fine, $race);

        return new JsonResponse($serializer->serialize($inf, 'json'), 201, [], true);
    }

    #[Route('/api/infractions', name: 'api_list_infractions', methods: ['GET'])]
    public function listInfractions(
        Request $request,
        InfractionRepository $repo,
        SerializerInterface $serializer
    ): JsonResponse {
        $team = $request->query->get('team');
        $driver = $request->query->get('driver');
        $date = $request->query->get('date');

        $infractions = $repo->findByFilters($team, $driver, $date);

        return new JsonResponse($serializer->serialize($infractions, 'json'), 200, [], true);
    }
}

