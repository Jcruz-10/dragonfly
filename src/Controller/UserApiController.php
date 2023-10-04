<?php

namespace App\Controller;

use App\Entity\User;
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
        private readonly ManagerRegistry $doctrine
    ) {
    }

    #[Route('/api/users', name: 'api_get_users')]
    public function getUsers(Request $request): JsonResponse
    {
        $key = $_ENV["JWT_SECRET"];
        $header = $request->headers->get('Authorization');
        $header = preg_replace('/^Bearer /', '', $header);
        try {
            $token = JWT::decode($header, new Key($key, 'HS256'));
            $user = $this->doctrine->getRepository(User::class)->find($token->sub);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ]);
        }
        return $this->json([
            'data' => $this->doctrine->getRepository(User::class)->findAll(),
        ]);
    }
}
