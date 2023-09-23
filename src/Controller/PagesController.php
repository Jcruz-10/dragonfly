<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
     #[Route('/', name: 'frontpage')]
    public function frontpage(): Response
    {
        return $this->render('frontpage.html.twig', [
            'title' => "Homepage Title",
        ]);
    }

     #[Route('/about', name: 'about')]
    public function about(): Response
    {
        return $this->render('about.html.twig', [
            'title' => "About Us",
        ]);
    }


}
