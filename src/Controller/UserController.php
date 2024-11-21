<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\EntityValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class UserController extends AbstractController
{
    #[Route('/users/new', name: 'new_user')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        EntityValidationService $validationService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setUsername($data["username"]);
        $user->setPassword($hasher->hashPassword($user, $data["password"]));
        $user->setRoles([]);
        $apiToken = bin2hex(random_bytes(32));
        $user->setApiToken($apiToken);

        $validationErrors = $validationService->validate($user);
        if (!empty($validationErrors)) {
            return new JsonResponse(["errors" => $validationErrors], 400);
        }

        $em->persist($user);
        $em->flush();
        return $this->json([
            "apiToken" => $apiToken
        ]);
    }
}
