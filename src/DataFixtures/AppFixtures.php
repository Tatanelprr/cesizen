<?php

namespace App\DataFixtures;

use App\Entity\Emotion;
use App\Entity\EmotionCategory;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $this->loadAdmin($manager);
        $this->loadEmotions($manager);
        $manager->flush();
    }

    private function loadAdmin(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@cesizen.fr');
        $admin->setPseudo('Administrateur');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin@cesizen1'));
        $manager->persist($admin);
    }

    private function loadEmotions(ObjectManager $manager): void
    {
        $data = [
            'Joie'      => ['color' => '#FFD740', 'emotions' => ['Fierté','Contentement','Enchantement','Excitation','Émerveillement','Gratitude']],
            'Colère'    => ['color' => '#E53935', 'emotions' => ['Frustration','Irritation','Rage','Ressentiment','Agacement','Hostilité']],
            'Peur'      => ['color' => '#7B1FA2', 'emotions' => ['Inquiétude','Anxiété','Terreur','Appréhension','Panique','Crainte']],
            'Tristesse' => ['color' => '#1565C0', 'emotions' => ['Chagrin','Mélancolie','Abattement','Désespoir','Solitude','Dépression']],
            'Surprise'  => ['color' => '#FF6F00', 'emotions' => ['Étonnement','Stupéfaction','Sidération','Incrédulité','Confusion']],
            'Dégoût'    => ['color' => '#2E7D32', 'emotions' => ['Répulsion','Déplaisir','Nausée','Dédain','Horreur','Dégoût profond']],
        ];

        foreach ($data as $catName => $catData) {
            $category = new EmotionCategory();
            $category->setLibelle($catName);
            $category->setCodeColor($catData['color']);
            $manager->persist($category);

            foreach ($catData['emotions'] as $emotionName) {
                $emotion = new Emotion();
                $emotion->setLibelle($emotionName);
                $emotion->setCodeColor($catData['color']);
                $emotion->setCategory($category);
                $manager->persist($emotion);
            }
        }
    }
}
