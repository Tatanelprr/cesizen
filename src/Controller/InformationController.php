<?php

namespace App\Controller;

use App\Repository\ArticleCategoryRepository;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/informations', name: 'app_information_')]
class InformationController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(
        Request $request,
        ArticleRepository $articleRepository,
        ArticleCategoryRepository $categoryRepository
    ): Response {
        $categoryId = $request->query->getInt('categorie');
        $categories = $categoryRepository->findAll();

        if ($categoryId) {
            $articles = $articleRepository->findBy(
                ['isPublished' => true, 'category' => $categoryId],
                ['publishedAt' => 'DESC']
            );
        } else {
            $articles = $articleRepository->findPublished();
        }

        return $this->render('information/index.html.twig', [
            'articles'           => $articles,
            'categories'         => $categories,
            'selectedCategoryId' => $categoryId,
        ]);
    }

    #[Route('/{slug}', name: 'show')]
    public function show(string $slug, ArticleRepository $articleRepository): Response
    {
        $article = $articleRepository->findOneBy(['slug' => $slug, 'isPublished' => true]);

        if (!$article) {
            throw $this->createNotFoundException('Article introuvable.');
        }

        return $this->render('information/show.html.twig', [
            'article' => $article,
        ]);
    }
}
