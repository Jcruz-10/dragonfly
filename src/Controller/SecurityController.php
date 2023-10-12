<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\AuthorizationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly AuthorizationService $authService
    ) {
    }

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

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    #[Route('/api/login', name: 'api_login')]
    public function apiLogin(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                 'error' => 'missing credentials',
             ], Response::HTTP_UNAUTHORIZED);
        }



        return $this->json([
            'data' => [
                'user' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
                'token' => $this->authService->getToken($user),
            ],
        ]);
    }
}
