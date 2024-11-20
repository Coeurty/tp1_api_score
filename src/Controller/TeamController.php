<?php

namespace App\Controller;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TeamController extends AbstractController
{
    #[Route('/teams/{id}', name: 'find_team_by_id', methods: ['GET'])]
    public function findTeamById(TeamRepository $teamRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $teams = $teamRepository->findOneBy(["id" => $id]);
        $jsonContent = $serializer->serialize($teams, 'json', ['groups' => 'team:read']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/teams', name: 'get_teams', methods: ['GET'])]
    public function getTeams(TeamRepository $teamRepository, SerializerInterface $serializer): JsonResponse
    {
        $teams = $teamRepository->findAll();
        $jsonContent = $serializer->serialize($teams, 'json', ['groups' => 'team:read']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/teams', name: 'create_team', methods: ['POST'])]
    public function createTeam(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // TODO: vérifier les données reçues

        $team = new Team();
        $team->setName($data['name']);

        $entityManager->persist($team);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($team, 'json', ['groups' => 'team:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/teams/{id}', name: 'update_team', methods: ['PUT'])]
    public function updateTeam(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, TeamRepository $teamRepository, int $id): JsonResponse
    {
        $team = $teamRepository->find($id);
        if (!$team) {
            return new JsonResponse(['error' => 'Team not found'], 404);
        }
        // $jsonContent = $serializer->serialize($team, 'json', ['groups' => 'team:read']);
        // return new JsonResponse($jsonContent, 201, [], true);

        $data = json_decode($request->getContent(), true);
        // TODO: vérifier les données reçues

        if (isset($data['name'])) {
            $team->setName($data['name']);
        }
        if (isset($data['score'])) {
            $team->setscore($data['score']);
        }

        $entityManager->persist($team);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($team, 'json', ['groups' => 'team:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/teams/{id}', name: 'delete_team', methods: ['DELETE'])]
    public function deleteTeam(int $id, TeamRepository $teamRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $team = $teamRepository->find($id);
        if (!$team) {
            return new JsonResponse(['error' => 'Team not found'], 404);
        }
        $entityManager->remove($team);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Team successfully deleted'], 200);
    }
}
