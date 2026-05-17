<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BreathingController extends AbstractController
{
    #[Route('/respiration', name: 'app_breathing')]
    public function index(): Response
    {
        $exercises = [
            '748' => ['name' => '7-4-8', 'inhale' => 7, 'hold' => 4, 'exhale' => 8, 'description' => 'Relaxation profonde'],
            '55'  => ['name' => '5-5',   'inhale' => 5, 'hold' => 0, 'exhale' => 5, 'description' => 'Cohérence cardiaque'],
            '46'  => ['name' => '4-6',   'inhale' => 4, 'hold' => 0, 'exhale' => 6, 'description' => 'Anti-stress'],
        ];

        return $this->render('breathing/index.html.twig', [
            'exercises' => $exercises,
        ]);
    }
}
