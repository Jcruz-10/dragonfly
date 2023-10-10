<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthorizationService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly AuthorizationService $authService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/api/users', name: 'api_get_users')]
    public function getUsers(Request $request): JsonResponse
    {
        //checking the login for current user
        $auth = $this->authService->checkToken($request);
        if (isset($auth['error'])) {
            return $this->json($auth);
        }
        if (!$auth['user']->isEmployee()) {
            return $this->json(['error' => 'User has insufficient permissions.']);
        }

        return $this->json(
            data: [
                'data' => $this->doctrine->getRepository(User::class)->findAll(),
            ],
            context: [
                'groups' => 'get_users',
            ]
        );
    }

    #[Route('/api/user/add', name: 'api_add_user')]
    public function addUser(Request $request): JsonResponse
    {
        //checking the login for current user
        $auth = $this->authService->checkToken($request);
        if (isset($auth['error'])) {
            return $this->json($auth);
        }
        if (!$auth['user']->isAdmin()) {
            return $this->json(['error' => 'User has insufficient permissions.']);
        }
        $payload = $request->getPayload()->all();

        if (empty($payload)) {
            return $this->json(['error' => 'Missing payload.']);
        }

        if (empty($payload['username']) || empty($payload['password'])) {
            return $this->json(['error' => 'Missing required data.']);
        }
        // @todo check for existing user
        $user = new User();
        $user->setUsername($payload['username']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $payload['password']));
        $user->setRoles($payload['roles'] ?? []);

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(
            data: [
                'data' => $user,
            ],
            context: [
                'groups' => 'get_users',
            ]
        );
    }
}
