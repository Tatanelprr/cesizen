<?php

namespace App\Controller;

use App\Entity\ActivityFavorite;
use App\Repository\ActivityFavoriteRepository;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/activites', name: 'app_activity_')]
class ActivityController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Request $request, ActivityRepository $activityRepository): Response
    {
        $type = $request->query->getString('type');
        $activities = $activityRepository->findActiveByType($type ?: null);

        $favoriteIds = [];
        if ($this->getUser()) {
            foreach ($this->getUser()->getJournalEntries() as $e) {
                // placeholder — favorites handled below
            }
        }

        return $this->render('activity/index.html.twig', [
            'activities'   => $activities,
            'selectedType' => $type,
            'types'        => ['meditation', 'sport', 'lecture', 'musique', 'nature', 'autre'],
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'])]
    public function show(int $id, ActivityRepository $activityRepository, ActivityFavoriteRepository $favoriteRepository): Response
    {
        $activity = $activityRepository->find($id);
        if (!$activity || !$activity->isActive()) {
            throw $this->createNotFoundException();
        }

        $isFavorite = false;
        if ($this->getUser()) {
            $isFavorite = (bool) $favoriteRepository->findOneByUserAndActivity($this->getUser(), $activity);
        }

        return $this->render('activity/show.html.twig', [
            'activity'   => $activity,
            'isFavorite' => $isFavorite,
        ]);
    }

    #[Route('/{id}/favori', name: 'toggle_favorite', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function toggleFavorite(
        int $id,
        Request $request,
        ActivityRepository $activityRepository,
        ActivityFavoriteRepository $favoriteRepository,
        EntityManagerInterface $em
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $activity = $activityRepository->find($id);
        if (!$activity) {
            throw $this->createNotFoundException();
        }

        if (!$this->isCsrfTokenValid('favorite-' . $id, $request->getPayload()->getString('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $existing = $favoriteRepository->findOneByUserAndActivity($this->getUser(), $activity);

        if ($existing) {
            $em->remove($existing);
            $this->addFlash('success', 'Retiré des favoris.');
        } else {
            $favorite = new ActivityFavorite();
            $favorite->setUser($this->getUser());
            $favorite->setActivity($activity);
            $em->persist($favorite);
            $this->addFlash('success', 'Ajouté aux favoris !');
        }

        $em->flush();
        return $this->redirectToRoute('app_activity_show', ['id' => $id]);
    }
}
