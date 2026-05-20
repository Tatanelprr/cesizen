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

## Prérequis

- **PHP** 8.2+
- **Composer** 2.x
- **Symfony CLI** 5.x
- **MySQL** 8.0 ou **MariaDB** 10.6+

---

## Démarrage rapide

### Windows

```powershell
.\start.ps1
```

### Linux / macOS

```bash
chmod +x start.sh
./start.sh
```

Les scripts vérifient automatiquement les dépendances, tentent de démarrer MySQL si nécessaire, lancent le serveur Symfony et ouvrent l'application dans le navigateur.

---

## Installation manuelle

### 1. Cloner le dépôt

```bash
git clone https://github.com/votre-compte/cesizen.git
cd cesizen
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l'environnement

```bash
cp .env .env.local
```

Modifier `.env.local` :

```env
DATABASE_URL="mysql://root:MOT_DE_PASSE@127.0.0.1:3306/cesizen?serverVersion=8.0&charset=utf8mb4"
APP_SECRET=une_chaine_aleatoire_32_caracteres
MAILER_DSN=null://null
```

### 4. Créer la base de données

```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

### 5. Charger les données

```bash
php bin/console doctrine:fixtures:load
```

> Réponds `yes` à la confirmation.

### 6. Lancer le serveur

```bash
symfony server:start --no-tls
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
