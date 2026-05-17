<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\Emotion;
use App\Entity\EmotionCategory;
use App\Entity\User;
use App\Form\ArticleFormType;
use App\Form\ArticleCategoryFormType;
use App\Form\EmotionFormType;
use App\Form\EmotionCategoryFormType;
use App\Repository\ArticleCategoryRepository;
use App\Repository\ArticleRepository;
use App\Repository\EmotionCategoryRepository;
use App\Repository\EmotionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin', name: 'app_admin_')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('', name: 'dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        ArticleRepository $articleRepository,
        EmotionRepository $emotionRepository
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'totalUsers'    => count($userRepository->findAll()),
            'totalArticles' => count($articleRepository->findAll()),
            'totalEmotions' => count($emotionRepository->findAll()),
        ]);
    }

    // ── Utilisateurs ──────────────────────────────────────────────────────────

    #[Route('/utilisateurs', name: 'users')]
    public function users(UserRepository $userRepository): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/utilisateurs/{id}/toggle', name: 'user_toggle', methods: ['POST'])]
    public function toggleUser(User $user, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggle-user-' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $user->setIsActive(!$user->isActive());
            $em->flush();
            $this->addFlash('success', 'Statut du compte modifié.');
        }
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/utilisateurs/{id}/supprimer', name: 'user_delete', methods: ['POST'])]
    public function deleteUser(User $user, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-user-' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Compte supprimé.');
        }
        return $this->redirectToRoute('app_admin_users');
    }

    // ── Articles ──────────────────────────────────────────────────────────────

    #[Route('/articles', name: 'articles')]
    public function articles(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/articles/index.html.twig', [
            'articles' => $articleRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/articles/nouveau', name: 'article_new')]
    public function newArticle(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug(strtolower($slugger->slug($article->getTitle())));
            if ($article->isPublished()) {
                $article->setPublishedAt(new \DateTimeImmutable());
            }
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Article créé.');
            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/articles/form.html.twig', ['form' => $form, 'article' => $article]);
    }

    #[Route('/articles/{id}/modifier', name: 'article_edit')]
    public function editArticle(Article $article, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setSlug(strtolower($slugger->slug($article->getTitle())));
            $article->setUpdatedAt(new \DateTimeImmutable());
            if ($article->isPublished() && !$article->getPublishedAt()) {
                $article->setPublishedAt(new \DateTimeImmutable());
            }
            $em->flush();
            $this->addFlash('success', 'Article modifié.');
            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('admin/articles/form.html.twig', ['form' => $form, 'article' => $article]);
    }

    #[Route('/articles/{id}/supprimer', name: 'article_delete', methods: ['POST'])]
    public function deleteArticle(Article $article, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete-article-' . $article->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article supprimé.');
        }
        return $this->redirectToRoute('app_admin_articles');
    }

    // ── Émotions ──────────────────────────────────────────────────────────────

    #[Route('/emotions', name: 'emotions')]
    public function emotions(EmotionCategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/emotions/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/emotions/categorie/nouvelle', name: 'emotion_category_new')]
    public function newEmotionCategory(Request $request, EntityManagerInterface $em): Response
    {
        $category = new EmotionCategory();
        $form = $this->createForm(EmotionCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Catégorie créée.');
            return $this->redirectToRoute('app_admin_emotions');
        }

        return $this->render('admin/emotions/category_form.html.twig', ['form' => $form]);
    }

    #[Route('/emotions/nouvelle', name: 'emotion_new')]
    public function newEmotion(Request $request, EntityManagerInterface $em): Response
    {
        $emotion = new Emotion();
        $form = $this->createForm(EmotionFormType::class, $emotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($emotion);
            $em->flush();
            $this->addFlash('success', 'Émotion créée.');
            return $this->redirectToRoute('app_admin_emotions');
        }

        return $this->render('admin/emotions/form.html.twig', ['form' => $form]);
    }

    #[Route('/emotions/{id}/toggle', name: 'emotion_toggle', methods: ['POST'])]
    public function toggleEmotion(Emotion $emotion, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggle-emotion-' . $emotion->getId(), $request->getPayload()->getString('_token'))) {
            $emotion->setIsActive(!$emotion->isActive());
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_emotions');
    }
}
