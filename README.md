# CESIZen — L'application de votre santé mentale

> Projet réalisé dans le cadre du titre **Concepteur Développeur d'Applications (CDA)** — CESI École d'ingénieurs 2025-2026
> Ethan Lepareur

---

## Présentation

CESIZen est une plateforme web de santé mentale commanditée par le Ministère de la Santé et de la Prévention. Elle propose des outils concrets pour aider les citoyens à mieux comprendre et gérer leur stress au quotidien.

### Modules disponibles

| Module | Accès |
|--------|-------|
| Informations & articles de prévention | Public |
| Diagnostic de stress (échelle Holmes & Rahe) | Public |
| Exercices de respiration (cohérence cardiaque) | Public + Créations personnelles (connecté) |
| Activités de détente (catalogue + favoris) | Public + Favoris (connecté) |
| Tracker d'émotions (journal + rapports) | Connecté |
| Back-office d'administration | Administrateur |

---

## Déploiement

| Environnement | URL | Branche |
|---|---|---|
| **Production** | https://cesizen-prod.up.railway.app | `main` |
| **Staging / Preprod** | https://cesizen-preprod.up.railway.app | `develop` |

Les déploiements sont automatiques via GitHub Actions après validation des tests PHPUnit. Tout ce qui va en production passe d'abord par le staging.

---

## Prérequis

| Outil | Version | Lien |
|---|---|---|
| PHP | 8.4+ | https://windows.php.net/download/ |
| Composer | 2.x | https://getcomposer.org/ |
| Symfony CLI | dernière | https://symfony.com/download |
| Docker Desktop | dernière | https://www.docker.com/products/docker-desktop/ |
| Git | 2.x | https://git-scm.com/ |

> **Docker Desktop** est la méthode recommandée pour la base de données MySQL (évite les conflits de ports et de permissions).

---

## Démarrage rapide (sessions suivantes)

```powershell
# 1. Démarrer MySQL si le conteneur est arrêté (après redémarrage PC)
docker start cesizen-mysql

# 2. Lancer le serveur Symfony (Windows)
.\start.ps1
```

```bash
# Linux / macOS
docker start cesizen-mysql
symfony server:start
```

`start.ps1` vérifie automatiquement PHP, Composer, Symfony CLI et MySQL, puis ouvre le navigateur sur `http://127.0.0.1:8000`.

---

## Utiliser l'application en ligne

L'application est déployée et accessible sans installation :

- **Production** → https://cesizen-prod.up.railway.app
- **Staging** → https://cesizen-preprod.up.railway.app

---

## Installation locale (développement)

### 1. Cloner le dépôt

```bash
git clone https://github.com/Tatanelprr/cesizen.git
cd cesizen
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env .env.local
```

Éditer `.env.local` :

```env
DATABASE_URL="mysql://root:MON_MOT_DE_PASSE@127.0.0.1:3306/cesizen?serverVersion=8.0&charset=utf8mb4"
APP_SECRET=une_chaine_aleatoire_32_caracteres
MAILER_DSN=null://null
GITHUB_TOKEN=           # token GitHub avec scope repo — pour le formulaire de feedback
GITHUB_REPO=Tatanelprr/cesizen
```

> Génère un APP_SECRET : `php -r "echo bin2hex(random_bytes(16));"`

### 4. Démarrer MySQL avec Docker

```bash
docker run -d \
  --name cesizen-mysql \
  -p 3306:3306 \
  -e MYSQL_ROOT_PASSWORD=MON_MOT_DE_PASSE \
  -e MYSQL_DATABASE=cesizen \
  mysql:8.0
```

### 5. Initialiser la base de données

```bash
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

### 6. Lancer le serveur

```powershell
# Windows — démarre MySQL Docker automatiquement
.\start.ps1
```

```bash
# Linux / macOS
docker start cesizen-mysql
symfony server:start
```

L'application est accessible sur `http://127.0.0.1:8000`

---

## Comptes disponibles

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@cesizen.fr | Admin@cesizen1 |
| Utilisateur démo | demo@cesizen.fr | Demo@cesizen1 |

---

## Tests

### Créer la base de test

```bash
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:create --env=test
```

### Lancer la suite

```bash
php bin/phpunit
```

Résultat attendu : **24 tests, 0 échec**

---

## Stack technique

| Couche | Technologie |
|--------|-------------|
| Backend | Symfony 7 + PHP 8.4 |
| ORM | Doctrine |
| Base de données | MySQL 8.0 |
| Frontend | Twig + Bootstrap 5 |
| Graphiques | Chart.js |
| Tests | PHPUnit 13 |
| Mobile | PWA (manifest + service worker) |

---

## Structure du projet

```
cesizen/
├── src/
│   ├── Controller/     # Contrôleurs MVC
│   ├── Entity/         # Entités Doctrine (Modèles)
│   ├── Form/           # Formulaires Symfony
│   ├── Repository/     # Requêtes base de données
│   ├── Security/       # Authentification
│   └── DataFixtures/   # Données initiales
├── templates/          # Vues Twig
├── public/             # Assets publics (manifest, sw.js)
├── tests/              # Tests PHPUnit
├── start.ps1           # Script démarrage Windows
├── start.sh            # Script démarrage Linux/Mac
└── .env                # Configuration
```

---

## Règle de déploiement — Staging avant Production

> **Tout ce qui va en production doit d'abord passer par `develop` et l'environnement staging.**

### Flux obligatoire

```
feature/* ──► develop (staging Railway) ──► main (production Railway)
```

Les sauts sont interdits : on ne peut jamais déployer en production un code qui n'a pas été validé en staging.

### Protections en place

**1. Vérification CI automatique**
À chaque push sur `main`, le job `Verify staging synced with production` vérifie que `develop` est bien un ancêtre du commit déployé. Si ce n'est pas le cas, le déploiement prod est bloqué automatiquement.

**2. Protection de branche GitHub**
La branche `main` exige :
- Une Pull Request (pas de push direct)
- Les status checks `Tests PHPUnit` et `Verify staging synced with production` doivent être verts

### Procédure standard

```bash
# 1. Développer sur une feature branch
git checkout -b feature/ma-fonctionnalite develop

# 2. PR feature → develop  →  deploy staging automatique
# 3. Valider sur https://cesizen-staging.up.railway.app
# 4. PR develop → main  →  deploy production automatique
```

Ne jamais merger directement dans `main` sans passer par `develop`.

---

## Accès rapides

| URL | Description |
|-----|-------------|
| `/` | Page d'accueil |
| `/informations` | Articles de prévention |
| `/diagnostic` | Questionnaire de stress |
| `/respiration` | Exercices de respiration |
| `/activites` | Catalogue d'activités |
| `/tracker` | Journal d'émotions (connecté) |
| `/admin` | Back-office (admin) |
| `/connexion` | Connexion |
| `/inscription` | Création de compte |

---

## Licence

Projet pédagogique — CESI École d'ingénieurs © 2025-2026
