<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Article;
use App\Entity\ArticleCategory;
use App\Entity\Emotion;
use App\Entity\JournalEntry;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DemoFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function getDependencies(): array
    {
        return [AppFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        $demoUser = $this->loadDemoUser($manager);
        $this->loadArticles($manager);
        $this->loadActivities($manager);
        $manager->flush();

        $emotions = $manager->getRepository(Emotion::class)->findAll();
        $admin    = $manager->getRepository(User::class)->findOneBy(['email' => 'admin@cesizen.fr']);

        $this->loadTracker($manager, $admin, $emotions);
        $this->loadTracker($manager, $demoUser, $emotions);
        $manager->flush();
    }

    private function loadDemoUser(ObjectManager $manager): User
    {
        $existing = $manager->getRepository(User::class)->findOneBy(['email' => 'demo@cesizen.fr']);
        if ($existing) return $existing;

        $user = new User();
        $user->setEmail('demo@cesizen.fr')
             ->setPseudo('Marie Dupont')
             ->setPassword($this->passwordHasher->hashPassword($user, 'Demo@cesizen1'));
        $manager->persist($user);
        return $user;
    }

    private function loadArticles(ObjectManager $manager): void
    {
        if ($manager->getRepository(Article::class)->count([]) > 0) return;

        $cats = [];
        foreach (['Stress', 'Anxiété', 'Bien-être', 'Sommeil', 'Relations'] as $name) {
            $cat = new ArticleCategory();
            $cat->setName($name);
            $manager->persist($cat);
            $cats[$name] = $cat;
        }
        $manager->flush();

        $articles = [
            ['Comprendre le stress : mécanismes et impacts', 'Stress',
                "Le stress est une réponse naturelle de l'organisme face à une situation perçue comme menaçante. Physiologiquement, il déclenche la libération d'adrénaline et de cortisol, préparant le corps à réagir.\n\nÀ court terme, le stress peut être bénéfique : il améliore la concentration et les performances. Mais lorsqu'il devient chronique, ses effets sont délétères : troubles du sommeil, irritabilité, affaiblissement du système immunitaire.\n\nIdentifier ses sources de stress est la première étape. Une fois identifiées, des stratégies adaptées peuvent être mises en place : respiration, activité physique, méditation."],
            ['La cohérence cardiaque : guide pratique', 'Bien-être',
                "La cohérence cardiaque est une technique de respiration qui agit directement sur le système nerveux autonome. En régulant le rythme cardiaque, elle induit un état de calme et de clarté mentale.\n\nLa méthode 365 est la plus connue : 3 fois par jour, 6 respirations par minute, pendant 5 minutes. Inspirez 5 secondes, expirez 5 secondes.\n\nLes bénéfices sont nombreux : réduction du cortisol, amélioration de la concentration, meilleure gestion émotionnelle."],
            ['Anxiété : distinguer l\'inquiétude normale du trouble anxieux', 'Anxiété',
                "L'anxiété est un sentiment d'appréhension face à un danger anticipé. Elle est normale quand elle nous prépare à affronter des situations difficiles. Elle devient problématique quand elle est disproportionnée et persistante.\n\nLes signes d'alerte : inquiétudes quasi permanentes, évitement de situations banales, symptômes physiques (palpitations, tensions musculaires).\n\nL'anxiété se traite efficacement par des thérapies cognitivo-comportementales et un accompagnement professionnel."],
            ['Améliorer la qualité de son sommeil', 'Sommeil',
                "Le sommeil est un pilier fondamental de la santé mentale. Un manque chronique augmente le risque de dépression et d'anxiété. Pourtant, 1 Français sur 3 souffre de troubles du sommeil.\n\nQuelques règles d'hygiène font une vraie différence : horaires fixes, éviter les écrans 1h avant le coucher, chambre fraîche et sombre, limiter la caféine après 14h.\n\nLa gestion du stress en journée est aussi déterminante : un esprit apaisé s'endort plus facilement."],
            ['Les bienfaits de la pleine conscience', 'Bien-être',
                "La pleine conscience est la capacité à porter son attention sur le moment présent, sans jugement. Des protocoles thérapeutiques reconnus scientifiquement l'ont intégrée avec succès.\n\nLa pratique régulière réduit significativement le stress, l'anxiété et les symptômes dépressifs. Elle améliore aussi la concentration et la régulation émotionnelle.\n\nCommencer est simple : 5 minutes par jour suffisent. Asseyez-vous, fermez les yeux, et portez votre attention sur votre respiration."],
            ['Construire des relations saines pour sa santé mentale', 'Relations',
                "Les liens sociaux sont un facteur protecteur majeur pour la santé mentale. La solitude chronique a des effets comparables au tabagisme sur la santé.\n\nUne relation saine se caractérise par la réciprocité, le respect et la communication ouverte. Elle ne doit pas être source d'épuisement permanent.\n\nSi vous ressentez un isolement, rejoindre une association ou reprendre contact avec des proches sont des actions concrètes efficaces."],
        ];

        foreach ($articles as [$title, $catName, $content]) {
            $article = new Article();
            $article->setTitle($title)
                    ->setSlug($this->slugify($title))
                    ->setContent($content)
                    ->setCategory($cats[$catName])
                    ->setIsPublished(true)
                    ->setPublishedAt(new \DateTimeImmutable());
            $manager->persist($article);
        }
    }

    private function loadActivities(ObjectManager $manager): void
    {
        if ($manager->getRepository(Activity::class)->count([]) > 0) return;

        $activities = [
            ['Méditation guidée pour débutants', 'meditation',
                "Cette séance de méditation guidée de 10 minutes est parfaite pour les débutants. Vous apprendrez à porter votre attention sur votre respiration et à observer vos pensées sans vous y accrocher.\n\nInstallez-vous confortablement dans un endroit calme. L'essentiel est d'être à l'aise. Laissez-vous guider sans chercher à « bien faire ».",
                'https://www.youtube.com/watch?v=inpok4MKVLM'],
            ['Yoga doux pour relâcher les tensions', 'sport',
                "Cette séance de yoga doux de 20 minutes cible les zones de tension les plus fréquentes : nuque, épaules, bas du dos. Idéale après une longue journée.\n\nPas besoin d'expérience préalable. Les mouvements sont lents et accessibles à tous les niveaux.",
                'https://www.youtube.com/watch?v=v7AYKMP6rOE'],
            ['Marche en nature : les bienfaits du shinrin-yoku', 'nature',
                "Le shinrin-yoku, ou bain de forêt, est une pratique japonaise qui consiste à se promener lentement en forêt en mobilisant tous ses sens. De nombreuses études démontrent ses effets positifs sur la réduction du cortisol.\n\n20 à 30 minutes suffisent. Pratiquez 2 à 3 fois par semaine.",
                null],
            ['Musique anti-stress : sons de la nature', 'musique',
                "Cette playlist de sons naturels favorise la relaxation profonde et la concentration. Les sons de la nature ont un effet prouvé sur la réduction de l'anxiété et l'amélioration du sommeil.\n\nIdéale pour accompagner une lecture, une méditation ou l'endormissement.",
                'https://www.youtube.com/watch?v=eKFTSSKCzWA'],
            ['Journal de gratitude : comment et pourquoi', 'lecture',
                "Le journal de gratitude est l'une des pratiques les mieux documentées pour améliorer le bien-être. En notant chaque jour 3 choses pour lesquelles vous êtes reconnaissant, vous entraînez votre cerveau à percevoir davantage le positif.\n\nAprès 4 à 6 semaines, la plupart des participants rapportent une amélioration notable de leur humeur.",
                null],
            ['Sophrologie : relaxation dynamique', 'meditation',
                "La sophrologie combine respiration, mouvements doux et visualisation positive. Cette séance guidée de 15 minutes vous fait découvrir les bases : tensio-relaxation, respiration abdominale et visualisation.\n\nLes effets se ressentent dès la première séance.",
                'https://www.youtube.com/watch?v=2n_qVcNvROk'],
        ];

        foreach ($activities as [$titre, $type, $description, $url]) {
            $activity = new Activity();
            $activity->setTitre($titre)->setType($type)
                     ->setDescription($description)->setUrlMedia($url)->setIsActive(true);
            $manager->persist($activity);
        }
    }

    private function loadTracker(ObjectManager $manager, User $user, array $emotions): void
    {
        if ($manager->getRepository(JournalEntry::class)->findOneBy(['user' => $user])) return;

        $byCategory = [];
        foreach ($emotions as $emotion) {
            $byCategory[$emotion->getCategory()->getLibelle()][] = $emotion;
        }

        $now = new \DateTimeImmutable();
        $patterns = [
            1  => ['Joie'=>40,'Tristesse'=>20,'Peur'=>15,'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            2  => ['Joie'=>35,'Tristesse'=>25,'Peur'=>20,'Colère'=>10,'Surprise'=>5, 'Dégoût'=>5],
            3  => ['Joie'=>45,'Tristesse'=>15,'Peur'=>15,'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            4  => ['Joie'=>50,'Tristesse'=>10,'Peur'=>10,'Colère'=>15,'Surprise'=>10,'Dégoût'=>5],
            5  => ['Joie'=>55,'Tristesse'=>10,'Peur'=>10,'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            6  => ['Joie'=>60,'Tristesse'=>10,'Peur'=>5, 'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            7  => ['Joie'=>65,'Tristesse'=>5, 'Peur'=>5, 'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            8  => ['Joie'=>60,'Tristesse'=>10,'Peur'=>5, 'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
            9  => ['Joie'=>40,'Tristesse'=>20,'Peur'=>15,'Colère'=>15,'Surprise'=>5, 'Dégoût'=>5],
            10 => ['Joie'=>35,'Tristesse'=>25,'Peur'=>20,'Colère'=>10,'Surprise'=>5, 'Dégoût'=>5],
            11 => ['Joie'=>30,'Tristesse'=>30,'Peur'=>20,'Colère'=>10,'Surprise'=>5, 'Dégoût'=>5],
            12 => ['Joie'=>50,'Tristesse'=>15,'Peur'=>10,'Colère'=>10,'Surprise'=>10,'Dégoût'=>5],
        ];

        for ($monthsAgo = 11; $monthsAgo >= 0; $monthsAgo--) {
            $monthDate   = $now->modify("-{$monthsAgo} months");
            $monthNum    = (int) $monthDate->format('n');
            $year        = (int) $monthDate->format('Y');
            $daysInMonth = (int) $monthDate->format('t');
            $pattern     = $patterns[$monthNum];

            for ($e = 0; $e < rand(18, 26); $e++) {
                $day  = rand(1, $daysInMonth);
                $hour = rand(7, 22);
                $min  = rand(0, 59);

                $date = \DateTimeImmutable::createFromFormat('Y-n-j H:i', "{$year}-{$monthNum}-{$day} {$hour}:{$min}");
                if (!$date || $date > $now) continue;

                $cat = $this->pickWeighted($pattern);
                if (empty($byCategory[$cat] ?? [])) continue;

                $emotion = $byCategory[$cat][array_rand($byCategory[$cat])];
                $intensite = match($cat) {
                    'Joie'      => rand(6, 10),
                    'Tristesse' => rand(4, 8),
                    'Peur'      => rand(4, 8),
                    'Colère'    => rand(5, 9),
                    default     => rand(3, 7),
                };

                $entry = new JournalEntry();
                $entry->setUser($user)->setEmotion($emotion)
                      ->setIntensite($intensite)->setDateCreation($date);
                $manager->persist($entry);
            }
        }
    }

    private function pickWeighted(array $weights): string
    {
        $rand = rand(1, array_sum($weights));
        $cumul = 0;
        foreach ($weights as $cat => $w) {
            $cumul += $w;
            if ($rand <= $cumul) return $cat;
        }
        return array_key_first($weights);
    }

    private function slugify(string $text): string
    {
        $text = strtolower($text);
        $map  = ['à'=>'a','á'=>'a','â'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e',
                 'î'=>'i','ï'=>'i','ô'=>'o','ö'=>'o','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c'];
        $text = strtr($text, $map);
        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        return trim($text, '-');
    }
}
