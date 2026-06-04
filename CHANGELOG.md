# Changelog

Toutes les modifications notables de CESIZen sont documentées dans ce fichier.

Format basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/).

---

## [1.0.0] - 2026-06-04

### Ajouts
- Module Comptes utilisateurs : inscription, connexion, profil, réinitialisation de mot de passe
- Module Informations : CMS articles de prévention avec catégories et filtrage
- Module Diagnostic de stress : questionnaire Holmes & Rahe (43 événements configurables), calcul de score, seuils de résultat paramétrables
- Module Exercices de respiration : cohérence cardiaque avec animation, exercices personnalisés par utilisateur
- Module Activités de détente : catalogue filtrable, fiches détail, système de favoris, intégration YouTube/Vimeo
- Module Tracker d'émotions : journal de bord, rapports graphiques Chart.js (semaine, mois, trimestre, année)
- Back-office administrateur : gestion utilisateurs, articles, émotions, diagnostic, activités
- PWA (Progressive Web App) : installation sur mobile via Chrome, mode hors-ligne partiel
- Scripts de démarrage automatisés : start.ps1 (Windows) et start.sh (Linux/macOS)
- Fixtures de données : admin, utilisateur démo, 33 émotions, 43 événements Holmes & Rahe, 6 articles, 6 activités, ~480 entrées de tracker

### Technique
- Architecture MVC avec Symfony 7 + PHP 8.4
- Base de données MySQL 8.0 via Doctrine ORM
- Interface Bootstrap 5 Mobile First avec bottom bar native
- Graphiques interactifs Chart.js
- 24 tests PHPUnit (unitaires, fonctionnels, non-régression)
- Pipeline CI/CD GitHub Actions — exécution automatique des tests sur chaque push
- Outil de ticketing GitHub Issues avec templates bug, évolution, sécurité

### Sécurité
- Hachage des mots de passe en Argon2id
- Protection CSRF sur tous les formulaires
- Protection XSS via échappement automatique Twig
- Protection injection SQL via Doctrine ORM (requêtes paramétrées)
- Contrôle d'accès par rôles (ROLE_USER, ROLE_ADMIN)
- Conformité RGPD : consentement CGU, droit à la suppression, hébergement UE

---

## [0.3.0] - 2026-05-20

### Ajouts
- Module Diagnostics (Holmes & Rahe) avec seuils configurables
- Module Activités de détente avec favoris
- Exercices de respiration personnalisés par utilisateur
- Données de démonstration (articles, activités, tracker 12 mois)

### Corrections
- Correction du UserBadge dans LoginFormAuthenticator (PHP 8.4)
- Correction du service worker (mode pass-through en développement)
- Correction des fixtures dupliquées (vérification d'existence avant insertion)

---

## [0.2.0] - 2026-05-17

### Ajouts
- Module Tracker d'émotions complet avec rapports Chart.js
- Module Informations (CMS articles)
- Templates admin (utilisateurs, articles, émotions)
- Exercices de respiration (cohérence cardiaque)
- PWA manifest.json + service worker
- Reset password (symfonycasts/reset-password-bundle)
- Fixtures de données initiales (admin + 33 émotions + Holmes & Rahe)

### Technique
- 24 tests PHPUnit configurés et passants
- Base de test cesizen_test isolée

---

## [0.1.0] - 2026-05-14

### Ajouts
- Initialisation du projet Symfony 7
- Entités de base : User, Article, Emotion, JournalEntry
- Authentification (inscription, connexion, déconnexion)
- Template de base Bootstrap 5 avec navigation mobile
- Configuration GitHub Actions CI/CD
- Templates GitHub Issues (bug, évolution, sécurité)
