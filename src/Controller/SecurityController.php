<?php

namespace App\Controller;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/api/login', name: 'api_login')]
    public function apiLogin(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                 'error' => 'missing credentials',
             ], Response::HTTP_UNAUTHORIZED);
        }

        $key = $_ENV["JWT_SECRET"];
        $payload = [
           'sub' => $user->getId(),
           'name' => $user->getUserIdentifier(),
           'iat' => time(),
           'exp' => time() + 14400,
        ];

        return $this->json([
            'data' => [
                'user' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
                'token' => JWT::encode($payload, $key, 'HS256'),
            ],
        ]);
    }
}
