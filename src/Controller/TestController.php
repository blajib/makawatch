<?php

namespace App\Controller;

use App\Entity\MakaUser;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/go', name: 'book')]
    public function goThere(): RedirectResponse
    {
        return $this->redirect('/');
    }
}