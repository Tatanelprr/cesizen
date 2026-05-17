<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DemoFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadArticles($manager);
        $this->loadActivities($manager);
        $manager->flush();
    }

    private function loadArticles(ObjectManager $manager): void
    {
        $categories = [];
        foreach (['Stress', 'Anxiété', 'Bien-être', 'Sommeil', 'Relations'] as $name) {
            $cat = new ArticleCategory();
            $cat->setName($name);
            $manager->persist($cat);
            $categories[$name] = $cat;
        }

        $articles = [
            [
                'title'    => 'Comprendre le stress : mécanismes et impacts',
                'category' => 'Stress',
                'content'  => "Le stress est une réponse naturelle de l'organisme face à une situation perçue comme menaçante ou exigeante. Physiologiquement, il déclenche la libération d'adrénaline et de cortisol, préparant le corps à réagir (fight or flight).\n\nA court terme, le stress peut être bénéfique : il améliore la concentration et les performances. Mais lorsqu'il devient chronique, ses effets sont délétères : troubles du sommeil, irritabilité, affaiblissement du système immunitaire, risques cardiovasculaires.\n\nIdentifier ses sources de stress est la première étape. Elles peuvent être professionnelles, familiales, financières ou liées à la santé. Une fois identifiées, des stratégies adaptées peuvent être mises en place : respiration, activité physique, méditation, organisation du temps.",
            ],
            [
                'title'    => 'La cohérence cardiaque : guide pratique',
                'category' => 'Bien-être',
                'content'  => "La cohérence cardiaque est une technique de respiration qui agit directement sur le système nerveux autonome. En régulant le rythme cardiaque, elle induit un état de calme et de clarté mentale.\n\nLa méthode 365 est la plus connue : 3 fois par jour, 6 respirations par minute, pendant 5 minutes. Concrètement, vous inspirez 5 secondes et expirez 5 secondes, de manière régulière.\n\nLes bénéfices sont nombreux et scientifiquement documentés : réduction du cortisol, amélioration de la concentration, meilleure gestion émotionnelle, renforcement du système immunitaire. Pratiquée régulièrement, elle devient un outil puissant de gestion du stress au quotidien.",
            ],
            [
                'title'    => 'Anxiété : distinguer l\'inquiétude normale de l\'anxiété pathologique',
                'category' => 'Anxiété',
                'content'  => "L'anxiété est un sentiment d'appréhension face à un danger anticipé. Elle est normale et utile quand elle nous prépare à affronter des situations difficiles. Elle devient problématique quand elle est disproportionnée, persistante et qu'elle interfère avec la vie quotidienne.\n\nLes signes d'alerte incluent : inquiétudes excessives quasi permanentes, évitement de situations banales, symptômes physiques (palpitations, tensions musculaires, maux de tête), troubles du sommeil.\n\nSi vous vous reconnaissez dans ces descriptions, n'hésitez pas à consulter un médecin ou un psychologue. L'anxiété se traite efficacement par des thérapies cognitivo-comportementales (TCC) et parfois un accompagnement médicamenteux.",
            ],
            [
                'title'    => 'Améliorer la qualité de son sommeil',
                'category' => 'Sommeil',
                'content'  => "Le sommeil est un pilier fondamental de la santé mentale. Un manque de sommeil chronique augmente le risque de dépression, d'anxiété et de troubles cognitifs. Pourtant, 1 Français sur 3 souffre de troubles du sommeil.\n\nQuelques règles d'hygiène du sommeil font une vraie différence : se coucher et se lever à des heures fixes, éviter les écrans 1h avant le coucher, maintenir une chambre fraîche et sombre, limiter la caféine après 14h, pratiquer une activité relaxante le soir (lecture, méditation, respiration).\n\nLa gestion du stress en journée est aussi déterminante : un esprit apaisé s'endort plus facilement. Le tracker d'émotions peut vous aider à identifier les journées où votre sommeil est perturbé.",
            ],
            [
                'title'    => 'Les bienfaits de la pleine conscience (mindfulness)',
                'category' => 'Bien-être',
                'content'  => "La pleine conscience, ou mindfulness, est la capacité à porter son attention sur le moment présent, sans jugement. Issue des traditions bouddhistes, elle a été intégrée dans de nombreux protocoles thérapeutiques reconnus scientifiquement.\n\nLa pratique régulière de la méditation de pleine conscience réduit significativement le stress, l'anxiété et les symptômes dépressifs. Elle améliore aussi la concentration, la régulation émotionnelle et la qualité des relations.\n\nCommencer est simple : 5 minutes par jour suffisent. Asseyez-vous confortablement, fermez les yeux, et portez votre attention sur votre respiration. Quand votre esprit s'égare (ce qui est normal), ramenez-le doucement sur la respiration sans vous juger.",
            ],
            [
                'title'    => 'Construire des relations saines pour sa santé mentale',
                'category' => 'Relations',
                'content'  => "Les liens sociaux sont un facteur protecteur majeur pour la santé mentale. Des études montrent que la solitude chronique a sur la santé des effets comparables au tabagisme. A l'inverse, des relations nourrissantes réduisent le stress et augmentent la résilience.\n\nUne relation saine se caractérise par la réciprocité, le respect, la communication ouverte et la possibilité d'être soi-même. Elle ne doit pas être source de pression ou d'épuisement permanent.\n\nSi vous ressentez un isolement, des actions concrètes aident : rejoindre une association, reprendre contact avec des proches perdus de vue, consulter un thérapeute pour travailler sur les freins relationnels.",
            ],
        ];

        foreach ($articles as $data) {
            $existing = $manager->getRepository(Article::class)->findOneBy(['title' => $data['title']]);
            if ($existing) continue;

            $article = new Article();
            $article->setTitle($data['title']);
            $article->setSlug($this->slugify($data['title']));
            $article->setContent($data['content']);
            $article->setCategory($categories[$data['category']]);
            $article->setIsPublished(true);
            $article->setPublishedAt(new \DateTimeImmutable());
            $manager->persist($article);
        }
    }

    private function loadActivities(ObjectManager $manager): void
    {
        $activities = [
            [
                'titre'       => 'Méditation guidée pour débutants',
                'type'        => 'meditation',
                'description' => "Cette séance de méditation guidée de 10 minutes est parfaite pour les débutants. Vous apprendrez à porter votre attention sur votre respiration et à observer vos pensées sans vous y accrocher.\n\nInstallez-vous confortablement dans un endroit calme. Vous pouvez vous asseoir sur une chaise, un coussin, ou vous allonger. L'essentiel est d'être à l'aise.\n\nLaissez-vous guider et ne cherchez pas à « bien faire » : il n'y a pas de bonne ou de mauvaise méditation. Chaque session est unique.",
                'urlMedia'    => 'https://www.youtube.com/watch?v=inpok4MKVLM',
            ],
            [
                'titre'       => 'Yoga doux pour relâcher les tensions',
                'type'        => 'sport',
                'description' => "Cette séance de yoga doux de 20 minutes cible les zones de tension les plus fréquentes : nuque, épaules, bas du dos. Idéale après une longue journée de travail ou une période de stress.\n\nPas besoin d'expérience préalable. Munissez-vous d'un tapis et portez des vêtements confortables. Les mouvements sont lents et accessibles à tous les niveaux.\n\nLe yoga combine étirements, renforcement musculaire doux et respiration consciente, ce qui en fait une pratique complète pour le corps et l'esprit.",
                'urlMedia'    => 'https://www.youtube.com/watch?v=v7AYKMP6rOE',
            ],
            [
                'titre'       => 'Marche en nature : les bienfaits du shinrin-yoku',
                'type'        => 'nature',
                'description' => "Le shinrin-yoku, ou « bain de forêt », est une pratique japonaise qui consiste à se promener lentement en forêt en mobilisant tous ses sens. De nombreuses études démontrent ses effets positifs sur la réduction du cortisol et l'amélioration de l'humeur.\n\nComment pratiquer : choisissez un espace naturel (forêt, parc), éteignez votre téléphone, marchez lentement sans destination précise. Observez les couleurs, écoutez les sons, touchez les textures, respirez les odeurs.\n\n20 à 30 minutes suffisent pour ressentir les bénéfices. Idéalement, pratiquez 2 à 3 fois par semaine.",
                'urlMedia'    => null,
            ],
            [
                'titre'       => 'Musique anti-stress : sons de la nature',
                'type'        => 'musique',
                'description' => "Cette playlist de sons naturels (pluie, forêt, océan, feu de cheminée) favorise la relaxation profonde et la concentration. Les sons de la nature ont un effet prouvé sur la réduction de l'anxiété et l'amélioration du sommeil.\n\nÉcoutez avec un casque pour une immersion maximale. Cette playlist est idéale pour accompagner une séance de lecture, de travail calme, de méditation ou pour faciliter l'endormissement.\n\nVolume recommandé : modéré, suffisant pour couvrir les bruits ambiants sans être agressif.",
                'urlMedia'    => 'https://www.youtube.com/watch?v=eKFTSSKCzWA',
            ],
            [
                'titre'       => 'Journal de gratitude : comment et pourquoi',
                'type'        => 'lecture',
                'description' => "Le journal de gratitude est l'une des pratiques les mieux documentées pour améliorer le bien-être mental. En notant chaque jour 3 choses pour lesquelles vous êtes reconnaissant, vous entraînez votre cerveau à percevoir davantage le positif.\n\nComment commencer : chaque soir avant de dormir, écrivez 3 éléments spécifiques de votre journée qui vous ont apporté de la satisfaction, même minimes. Soyez précis plutôt que général.\n\nAprès 4 à 6 semaines de pratique régulière, la plupart des participants rapportent une amélioration notable de leur humeur générale et une réduction de l'anxiété.",
                'urlMedia'    => null,
            ],
            [
                'titre'       => 'Sophrologie : relaxation dynamique',
                'type'        => 'meditation',
                'description' => "La sophrologie est une technique de relaxation qui combine respiration, mouvements doux et visualisation positive. Développée dans les années 1960, elle est aujourd'hui utilisée dans de nombreux contextes : gestion du stress, préparation aux examens, accompagnement de la douleur.\n\nCette séance guidée de 15 minutes vous fait découvrir les bases : la tensio-relaxation (contracter puis relâcher les muscles), la respiration abdominale et la visualisation d'un lieu de bien-être.\n\nPratiquez de préférence dans un endroit calme, assis ou allongé. Les effets se ressentent dès la première séance.",
                'urlMedia'    => 'https://www.youtube.com/watch?v=2n_qVcNvROk',
            ],
        ];

        foreach ($activities as $data) {
            $existing = $manager->getRepository(Activity::class)->findOneBy(['titre' => $data['titre']]);
            if ($existing) continue;

            $activity = new Activity();
            $activity->setTitre($data['titre']);
            $activity->setType($data['type']);
            $activity->setDescription($data['description']);
            $activity->setUrlMedia($data['urlMedia']);
            $activity->setIsActive(true);
            $manager->persist($activity);
        }
    }

    private function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[àáâãäå]/u', 'a', $text);
        $text = preg_replace('/[éèêë]/u', 'e', $text);
        $text = preg_replace('/[îï]/u', 'i', $text);
        $text = preg_replace('/[ôö]/u', 'o', $text);
        $text = preg_replace('/[ùûü]/u', 'u', $text);
        $text = preg_replace('/[ç]/u', 'c', $text);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
