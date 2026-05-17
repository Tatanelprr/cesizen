<?php

namespace App\DataFixtures;

use App\Entity\DiagnosticEvent;
use App\Entity\DiagnosticThreshold;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DiagnosticFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadEvents($manager);
        $this->loadThresholds($manager);
        $manager->flush();
    }

    private function loadEvents(ObjectManager $manager): void
    {
        $events = [
            ['Décès du conjoint', 100], ['Divorce', 73], ['Séparation conjugale', 65],
            ['Emprisonnement', 63], ['Décès d\'un proche', 63], ['Blessure ou maladie personnelle', 53],
            ['Mariage', 50], ['Licenciement', 47], ['Réconciliation conjugale', 45],
            ['Retraite', 45], ['Problème de santé d\'un proche', 44], ['Grossesse', 40],
            ['Difficultés sexuelles', 39], ['Naissance ou adoption', 39], ['Réorganisation professionnelle', 39],
            ['Changement de situation financière', 38], ['Décès d\'un ami proche', 37],
            ['Changement de métier', 36], ['Conflits conjugaux', 35], ['Emprunt important', 31],
            ['Saisie d\'un emprunt', 30], ['Changement de responsabilités', 29], ['Départ d\'un enfant', 29],
            ['Problèmes avec la belle-famille', 29], ['Succès personnel important', 28],
            ['Arrêt ou reprise de travail du conjoint', 26], ['Début ou fin de scolarité', 26],
            ['Changement de conditions de vie', 25], ['Changement d\'habitudes personnelles', 24],
            ['Problèmes avec la hiérarchie', 23], ['Changement d\'horaires', 20],
            ['Déménagement', 20], ['Changement d\'école', 20], ['Changement de loisirs', 19],
            ['Changement d\'activités religieuses', 19], ['Changement d\'activités sociales', 18],
            ['Emprunt mineur', 17], ['Changement du rythme de sommeil', 16],
            ['Changements de réunions familiales', 15], ['Changement d\'habitudes alimentaires', 15],
            ['Vacances', 13], ['Période de fêtes', 12], ['Infractions mineures à la loi', 11],
        ];

        foreach ($events as $pos => [$libelle, $points]) {
            $event = new DiagnosticEvent();
            $event->setLibelle($libelle)->setPoints($points)->setPosition($pos);
            $manager->persist($event);
        }
    }

    private function loadThresholds(ObjectManager $manager): void
    {
        $thresholds = [
            [0, 149, 'Stress faible', '#00C853',
                'Votre niveau de stress est bas. Vous gérez bien les changements de votre vie.',
                'Continuez à prendre soin de vous avec des activités de détente et de respiration.'],
            [150, 299, 'Stress modéré', '#FFD740',
                'Vous avez vécu des changements significatifs. Votre organisme est sous pression.',
                'Pratiquez régulièrement des exercices de respiration et identifiez vos émotions avec le tracker.'],
            [300, 9999, 'Stress élevé', '#E53935',
                'Vous avez accumulé beaucoup de changements. Le risque de maladie liée au stress est important.',
                'Il est fortement recommandé de consulter un professionnel de santé. En attendant, nos exercices de cohérence cardiaque peuvent vous aider.'],
        ];

        foreach ($thresholds as [$min, $max, $niveau, $color, $desc, $conseil]) {
            $t = new DiagnosticThreshold();
            $t->setScoreMin($min)->setScoreMax($max)->setNiveau($niveau)
              ->setCodeColor($color)->setDescription($desc)->setConseil($conseil);
            $manager->persist($t);
        }
    }
}
