<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthorizationService;
use Doctrine\Persistence\ManagerRegistry;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserApiController extends AbstractController
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
        private readonly AuthorizationService $authService
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
        if (!in_array('ROLE_ADMIN', $auth['user']->getRoles())) {
            return $this->json(['error' => 'User has insufficient permissions.']);
        }

        return $this->json([
            'data' => $this->doctrine->getRepository(User::class)->findAll(),
        ]);
    }
}
