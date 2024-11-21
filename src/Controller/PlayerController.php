<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Service\EntityValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PlayerController extends AbstractController
{
    #[Route('/players/{id}', name: 'player_by_id', methods: ['GET'])]
    public function findPlayerById(PlayerRepository $playerRepository, int $id): JsonResponse
    {
        $player = $playerRepository->findOneBy(["id" => $id]);
        return $this->json($player, 200, [], ["groups" => "player:read"]);
    }

    #[Route('/players', name: 'get_player', methods: ['GET'])]
    public function getPlayers(PlayerRepository $playerRepository): JsonResponse
    {
        $players = $playerRepository->findAll();
        return $this->json($players, 200, [], ["groups" => "player:read"]);
    }

    #[Route('/players', name: 'create_player', methods: ['POST'])]
    public function createPlayer(Request $request, EntityManagerInterface $entityManager, TeamRepository $teamRepository, EntityValidationService $validationService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        // TODO: vérifier les données reçues

        $player = new Player();
        $player->setFirstname($data['firstname']);
        $player->setLastname($data['lastname']);
        if (isset($data['team'])) {
            $team = $teamRepository->find($data['team']);
            if (!$team) {
                return new JsonResponse(["error" => "Team not found"], 404);
            }
            $player->setTeam($team);
        }

        $validationErrors = $validationService->validate($player, ['create']);
        if (!empty($validationErrors)) {
            return new JsonResponse(["errors" => $validationErrors], 400);
        }

        $entityManager->persist($player);
        $entityManager->flush();

        return $this->json($player, 200, [], ["groups" => "player:read"]);
    }

    #[Route('/players/{id}', name: 'update_player', methods: ['PUT'])]
    public function updatePlayer(Request $request, EntityManagerInterface $entityManager, PlayerRepository $playerRepository, TeamRepository $teamRepository, EntityValidationService $validationService, int $id): JsonResponse
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

        $validationErrors = $validationService->validate($player);
        if (!empty($validationErrors)) {
            return new JsonResponse(["errors" => $validationErrors], 400);
        }

        $entityManager->persist($player);
        $entityManager->flush();

        $this->json($player, 200, [], ["groups" => "player:read"]);
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
