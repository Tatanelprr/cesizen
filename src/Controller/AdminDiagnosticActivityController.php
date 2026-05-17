<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\DiagnosticEvent;
use App\Entity\DiagnosticThreshold;
use App\Form\ActivityFormType;
use App\Form\DiagnosticEventFormType;
use App\Form\DiagnosticThresholdFormType;
use App\Repository\ActivityRepository;
use App\Repository\DiagnosticEventRepository;
use App\Repository\DiagnosticThresholdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminDiagnosticActivityController extends AbstractController
{
    // ── Diagnostic ────────────────────────────────────────────────────────────

    #[Route('/diagnostic', name: 'diagnostic_index')]
    public function diagnosticIndex(
        DiagnosticEventRepository $eventRepo,
        DiagnosticThresholdRepository $thresholdRepo
    ): Response {
        return $this->render('admin/diagnostic/index.html.twig', [
            'events'     => $eventRepo->findBy([], ['position' => 'ASC']),
            'thresholds' => $thresholdRepo->findBy([], ['scoreMin' => 'ASC']),
        ]);
    }

    #[Route('/diagnostic/evenement/nouveau', name: 'diagnostic_event_new')]
    public function newEvent(Request $request, EntityManagerInterface $em): Response
    {
        $event = new DiagnosticEvent();
        $form  = $this->createForm(DiagnosticEventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Événement créé.');
            return $this->redirectToRoute('app_admin_diagnostic_index');
        }

        return $this->render('admin/diagnostic/event_form.html.twig', ['form' => $form, 'event' => $event]);
    }

    #[Route('/diagnostic/evenement/{id}/modifier', name: 'diagnostic_event_edit')]
    public function editEvent(DiagnosticEvent $event, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DiagnosticEventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Événement modifié.');
            return $this->redirectToRoute('app_admin_diagnostic_index');
        }

        return $this->render('admin/diagnostic/event_form.html.twig', ['form' => $form, 'event' => $event]);
    }

    #[Route('/diagnostic/evenement/{id}/supprimer', name: 'diagnostic_event_delete', methods: ['POST'])]
    public function deleteEvent(DiagnosticEvent $event, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete-event-' . $event->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($event);
            $em->flush();
            $this->addFlash('success', 'Événement supprimé.');
        }
        return $this->redirectToRoute('app_admin_diagnostic_index');
    }

    #[Route('/diagnostic/seuil/nouveau', name: 'diagnostic_threshold_new')]
    public function newThreshold(Request $request, EntityManagerInterface $em): Response
    {
        $threshold = new DiagnosticThreshold();
        $form      = $this->createForm(DiagnosticThresholdFormType::class, $threshold);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($threshold);
            $em->flush();
            $this->addFlash('success', 'Seuil créé.');
            return $this->redirectToRoute('app_admin_diagnostic_index');
        }

        return $this->render('admin/diagnostic/threshold_form.html.twig', ['form' => $form]);
    }

    #[Route('/diagnostic/seuil/{id}/modifier', name: 'diagnostic_threshold_edit')]
    public function editThreshold(DiagnosticThreshold $threshold, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(DiagnosticThresholdFormType::class, $threshold);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Seuil modifié.');
            return $this->redirectToRoute('app_admin_diagnostic_index');
        }

        return $this->render('admin/diagnostic/threshold_form.html.twig', ['form' => $form]);
    }

    // ── Activités ─────────────────────────────────────────────────────────────

    #[Route('/activites', name: 'activities')]
    public function activities(ActivityRepository $activityRepository): Response
    {
        return $this->render('admin/activities/index.html.twig', [
            'activities' => $activityRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/activites/nouvelle', name: 'activity_new')]
    public function newActivity(Request $request, EntityManagerInterface $em): Response
    {
        $activity = new Activity();
        $form     = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($activity);
            $em->flush();
            $this->addFlash('success', 'Activité créée.');
            return $this->redirectToRoute('app_admin_activities');
        }

        return $this->render('admin/activities/form.html.twig', ['form' => $form, 'activity' => $activity]);
    }

    #[Route('/activites/{id}/modifier', name: 'activity_edit')]
    public function editActivity(Activity $activity, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ActivityFormType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Activité modifiée.');
            return $this->redirectToRoute('app_admin_activities');
        }

        return $this->render('admin/activities/form.html.twig', ['form' => $form, 'activity' => $activity]);
    }

    #[Route('/activites/{id}/toggle', name: 'activity_toggle', methods: ['POST'])]
    public function toggleActivity(Activity $activity, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('toggle-activity-' . $activity->getId(), $request->getPayload()->getString('_token'))) {
            $activity->setIsActive(!$activity->isActive());
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_activities');
    }
}
