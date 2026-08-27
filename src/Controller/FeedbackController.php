<?php

namespace App\Controller;

use App\Form\FeedbackFormType;
use App\Service\GithubIssueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FeedbackController extends AbstractController
{
    private const LABEL_MAP = [
        'bug' => ['bug'],
        'idea' => ['enhancement'],
        'other' => ['question'],
    ];

    #[Route('/feedback', name: 'app_feedback')]
    public function index(Request $request, GithubIssueService $githubIssueService): Response
    {
        $form = $this->createForm(FeedbackFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $labels = self::LABEL_MAP[$data['type']] ?? [];

            $body = $data['description'];
            if (!empty($data['email'])) {
                $body .= "\n\n---\n**Contact :** " . $data['email'];
            }
            $body .= "\n\n*Signalé via CESIZen*";

            $success = $githubIssueService->createIssue($data['titre'], $body, $labels);

            if ($success) {
                $this->addFlash('success', 'Merci pour votre retour ! Il a bien été transmis à notre équipe.');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.');
            }

            return $this->redirectToRoute('app_feedback');
        }

        return $this->render('feedback/index.html.twig', [
            'form' => $form,
        ]);
    }
}
