<?php

namespace App\Controller;

use App\Entity\BreathingExercise;
use App\Form\BreathingExerciseFormType;
use App\Repository\BreathingExerciseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/respiration', name: 'app_breathing')]
class BreathingController extends AbstractController
{
    #[Route('', name: '')]
    public function index(Request $request, BreathingExerciseRepository $repo, EntityManagerInterface $em): Response
    {
        $exercises = $repo->findForUser($this->getUser());

        $newExercise = null;
        $form        = null;

        if ($this->getUser()) {
            $newExercise = new BreathingExercise();
            $form        = $this->createForm(BreathingExerciseFormType::class, $newExercise);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $newExercise->setUser($this->getUser());
                $em->persist($newExercise);
                $em->flush();
                $this->addFlash('success', 'Exercice créé !');
                return $this->redirectToRoute('app_breathing');
            }
        }

        return $this->render('breathing/index.html.twig', [
            'exercises' => $exercises,
            'form'      => $form?->createView(),
        ]);
    }

    #[Route('/{id}/supprimer', name: '_delete', methods: ['POST'])]
    public function delete(BreathingExercise $exercise, Request $request, EntityManagerInterface $em): Response
    {
        if ($exercise->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete-breathing-' . $exercise->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($exercise);
            $em->flush();
            $this->addFlash('success', 'Exercice supprimé.');
        }

        return $this->redirectToRoute('app_breathing');
    }
}
