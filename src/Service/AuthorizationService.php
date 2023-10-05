<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\HttpFoundation\Request;

class AuthorizationService
{
    public function __construct(
        private readonly ManagerRegistry $doctrine
    ) {
    }

    public function checkToken(Request $request): array
    {
        $key = $_ENV["JWT_SECRET"];
        $header = $request->headers->get('Authorization');
        $header = preg_replace('/^Bearer /', '', $header);
        try {
            $token = JWT::decode($header, new Key($key, 'HS256'));
            $user = $this->doctrine->getRepository(User::class)->find($token->sub);
            return ['user' => $user];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
