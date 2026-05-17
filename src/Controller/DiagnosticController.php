<?php

namespace App\Controller;

use App\Repository\DiagnosticEventRepository;
use App\Repository\DiagnosticThresholdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/diagnostic', name: 'app_diagnostic_')]
class DiagnosticController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(DiagnosticEventRepository $eventRepository): Response
    {
        return $this->render('diagnostic/index.html.twig', [
            'events' => $eventRepository->findActive(),
        ]);
    }

    #[Route('/resultat', name: 'result', methods: ['POST'])]
    public function result(
        Request $request,
        DiagnosticEventRepository $eventRepository,
        DiagnosticThresholdRepository $thresholdRepository
    ): Response {
        $selectedIds = $request->getPayload()->all('events') ?? [];
        $events = $eventRepository->findActive();

        $score = 0;
        $selectedEvents = [];

        foreach ($events as $event) {
            if (in_array((string) $event->getId(), $selectedIds)) {
                $score += $event->getPoints();
                $selectedEvents[] = $event;
            }
        }

        $threshold = $thresholdRepository->findForScore($score);

        return $this->render('diagnostic/result.html.twig', [
            'score'          => $score,
            'selectedEvents' => $selectedEvents,
            'threshold'      => $threshold,
        ]);
    }
}
