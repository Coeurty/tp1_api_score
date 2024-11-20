<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PlayerController extends AbstractController
{
    #[Route('/players/{id}', name: 'player_by_id', methods: ['GET'])]
    public function findPlayerById(PlayerRepository $playerRepository, SerializerInterface $serializer, int $id): JsonResponse
    {
        $players = $playerRepository->findOneBy(["id" => $id]);
        $jsonContent = $serializer->serialize($players, 'json', ['groups' => 'player:read']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/players', name: 'get_player', methods: ['GET'])]
    public function getPlayers(PlayerRepository $playerRepository, SerializerInterface $serializer): JsonResponse
    {
        $players = $playerRepository->findAll();
        $jsonContent = $serializer->serialize($players, 'json', ['groups' => 'player:read']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // TODO: vérifier les données reçues

        $player = new Player();
        $player->setFirstname($data['firstname']);
        $player->setLastname($data['lastname']);

        $entityManager->persist($player);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($player, 'json', ['groups' => 'player:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/players/{id}', name: 'update_player', methods: ['PUT'])]
    public function updatePlayer(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, PlayerRepository $playerRepository, TeamRepository $teamRepository, int $id): JsonResponse
    {
        $player = $playerRepository->find($id);
        if (!$player) {
            return new JsonResponse(['error' => 'Player not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        // TODO: vérifier les données reçues

        if (isset($data['firstname'])) {
            $player->setFirstname($data['firstname']);
        }
        if (isset($data['lastname'])) {
            $player->setLastname($data['lastname']);
        }
        if (isset($data['team'])) {
            $team = $teamRepository->find($data['team']);
            if (!$team) {
                return new JsonResponse(["error" => "Team not found"], 404);
            }
            $player->setTeam($team);
        }

        $entityManager->persist($player);
        $entityManager->flush();

        $jsonContent = $serializer->serialize($player, 'json', ['groups' => 'player:read']);
        return new JsonResponse($jsonContent, 201, [], true);
    }

    #[Route('/players/{id}', name: 'delete_player', methods: ['DELETE'])]
    public function deletePlayer(int $id, PlayerRepository $playerRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $player = $playerRepository->find($id);

        if (!$player) {
            return new JsonResponse(['error' => 'Player not found'], 404);
        }

        $entityManager->remove($player);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Player successfully deleted'], 200);
    }
}
