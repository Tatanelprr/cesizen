<?php

namespace App\Controller;

use App\Entity\JournalEntry;
use App\Form\JournalEntryFormType;
use App\Repository\EmotionCategoryRepository;
use App\Repository\JournalEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tracker', name: 'app_tracker_')]
#[IsGranted('ROLE_USER')]
class TrackerController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(JournalEntryRepository $journalEntryRepository): Response
    {
        $user = $this->getUser();
        $entries = $journalEntryRepository->findBy(
            ['user' => $user],
            ['dateCreation' => 'DESC'],
            30
        );

        return $this->render('tracker/index.html.twig', [
            'entries' => $entries,
        ]);
    }

    #[Route('/ajouter', name: 'new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        EmotionCategoryRepository $categoryRepository
    ): Response {
        $entry = new JournalEntry();
        $entry->setUser($this->getUser());

        $form = $this->createForm(JournalEntryFormType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entry);
            $em->flush();
            $this->addFlash('success', 'Entrée ajoutée à votre journal.');
            return $this->redirectToRoute('app_tracker_index');
        }

        $categories = $categoryRepository->findAll();

        return $this->render('tracker/new.html.twig', [
            'form'       => $form,
            'categories' => $categories,
        ]);
    }

    #[Route('/{id}/modifier', name: 'edit')]
    public function edit(JournalEntry $entry, Request $request, EntityManagerInterface $em): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(JournalEntryFormType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Entrée mise à jour.');
            return $this->redirectToRoute('app_tracker_index');
        }

        return $this->render('tracker/edit.html.twig', [
            'form'  => $form,
            'entry' => $entry,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'delete', methods: ['POST'])]
    public function delete(JournalEntry $entry, Request $request, EntityManagerInterface $em): Response
    {
        if ($entry->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete-entry-' . $entry->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($entry);
            $em->flush();
            $this->addFlash('success', 'Entrée supprimée.');
        }

        return $this->redirectToRoute('app_tracker_index');
    }

    #[Route('/rapport', name: 'report')]
    public function report(Request $request, JournalEntryRepository $journalEntryRepository): Response
    {
        $period = $request->query->getString('periode', 'semaine');

        $now = new \DateTimeImmutable();
        $from = match ($period) {
            'mois'      => $now->modify('-1 month'),
            'trimestre' => $now->modify('-3 months'),
            'annee'     => $now->modify('-1 year'),
            default     => $now->modify('-7 days'),
        };

        $entries = $journalEntryRepository->findByUserAndPeriod($this->getUser(), $from, $now);

        $chartData = $this->buildChartData($entries);

        return $this->render('tracker/report.html.twig', [
            'entries'   => $entries,
            'period'    => $period,
            'chartData' => $chartData,
            'from'      => $from,
            'to'        => $now,
        ]);
    }

    private function buildChartData(array $entries): array
    {
        $byCategory = [];

        foreach ($entries as $entry) {
            $cat = $entry->getEmotion()->getCategory()->getLibelle();
            $color = $entry->getEmotion()->getCategory()->getCodeColor();
            if (!isset($byCategory[$cat])) {
                $byCategory[$cat] = ['count' => 0, 'color' => $color];
            }
            $byCategory[$cat]['count']++;
        }

        return [
            'labels'     => array_keys($byCategory),
            'data'       => array_column(array_values($byCategory), 'count'),
            'colors'     => array_column(array_values($byCategory), 'color'),
        ];
    }
}
