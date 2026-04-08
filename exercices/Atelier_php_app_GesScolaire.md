---
marp: false
size: 4:3
style: |
  h2, h3, p {
    font-size: 20px;
  }
  li {
    font-size:20px
  }
headingDivider: 1
header: 
paginate: true
footer: Licence 2 Informatique &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ISI (Institut Supérieur D'Informatique) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RD
---
<img src="../isi.png" alt="ISI" width="100px">


# Atelier PHP — Plateforme Web de Gestion Scolaire

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   ![miniature](php.png)

## Par Robert DIASSÉ


**Module :** Développement Web avec PHP  
**Niveau :** Licence 2  
**Durée estimée :** 6 à 8 heures  
**Prérequis :** HTML/CSS, bases de MySQL, notions de PHP (variables, fonctions, tableaux)

---

## Objectifs de l'atelier

À l'issue de cet atelier, vous serez capable de :

- Analyser un cahier des charges et en extraire les besoins fonctionnels
- Concevoir une base de données relationnelle adaptée à un projet réel
- Structurer une application PHP de manière professionnelle
- Implémenter un système d'authentification sécurisé
- Développer des modules CRUD (Create, Read, Update, Delete) complets
- Générer dynamiquement des bulletins de notes
- Produire une application web fonctionnelle de bout en bout

---

## Présentation du projet

Vous allez développer une **plateforme web de gestion scolaire** qui centralise toutes les opérations académiques et administratives d'un établissement. Cette application comprendra :

| Module | Description |
|---|---|
| Authentification | Connexion sécurisée avec gestion des rôles |
| Tableau de bord | Vue synthétique des statistiques |
| Gestion des étudiants | Ajout, modification, suppression, consultation |
| Gestion des enseignants | Profils et affectations |
| Gestion des classes | Organisation des promotions |
| Gestion des matières | Catalogue des cours |
| Gestion des notes | Saisie et calcul des moyennes |
| Bulletins | Génération automatique par étudiant |
| Classement | Palmarès et attribution des distinctions |

---

## Étape 1 — Analyse du projet et définition des besoins

### 1.1 Identification des acteurs

Avant d'écrire une seule ligne de code, il faut comprendre **qui utilise l'application** et **ce qu'il peut faire**.

| Acteur | Rôle dans le système |
|---|---|
| **Administrateur** | Accès total : gère les utilisateurs, les classes, les paramètres |
| **Enseignant** | Saisit les notes de ses matières |
| **Étudiant** *(optionnel)* | Consulte ses propres notes et bulletins |

### 1.2 Besoins fonctionnels par module

#### Module 1 — Gestion des étudiants
- Enregistrer un nouvel étudiant (nom, prénom, date de naissance, adresse, contact)
- Modifier les informations d'un étudiant existant
- Supprimer un étudiant du système
- Rechercher un étudiant par nom ou classe
- Consulter la fiche complète d'un étudiant

#### Module 2 — Gestion des enseignants
- Enregistrer un enseignant avec sa spécialité
- Affecter un enseignant à une ou plusieurs matières
- Consulter la liste des enseignants par matière

#### Module 3 — Gestion des classes
- Créer des classes (ex. : L2-Info-A, L2-Info-B)
- Associer des étudiants à une classe
- Consulter les effectifs par classe

#### Module 4 — Gestion des matières
- Créer une matière avec son coefficient
- Associer une matière à une classe
- Affecter un enseignant responsable

#### Module 5 — Gestion des notes
- Saisir les notes d'un étudiant pour une matière
- Calculer automatiquement la moyenne par matière et générale
- Appliquer les coefficients

#### Module 6 — Bulletins et classement
- Générer le bulletin d'un étudiant
- Classer les étudiants par moyenne dans leur classe
- Attribuer des mentions (Passable, Assez Bien, Bien, Très Bien)
- Désigner les premiers de classe

### 1.3 Besoins non fonctionnels
- Interface responsive et lisible
- Sécurité : mots de passe hachés, sessions protégées
- Navigation intuitive pour des non-techniciens

---

> ### ✅ Point de vérification — Étape 1
> Avant de continuer, répondez aux questions suivantes :
> 1. Citez les 3 acteurs du système et leur niveau d'accès.
> 2. Qu'est-ce qu'un besoin fonctionnel ? Donnez un exemple tiré du projet.
> 3. Pourquoi le coefficient d'une matière est-il important dans le calcul de la moyenne ?
> 4. Quel est le lien entre un étudiant, une classe, et une matière ?
>
> ➡️ Discutez vos réponses avec votre voisin avant de passer à l'étape suivante.

---

## Étape 2 — Conception de la base de données

### 2.1 Identification des entités et relations

À partir de l'analyse, voici les tables dont nous avons besoin :

```
utilisateurs     → gère les comptes de connexion
etudiants        → informations personnelles des étudiants
enseignants      → informations personnelles des enseignants
classes          → les promotions/groupes
matieres         → les cours avec leurs coefficients
inscriptions     → rattachement étudiant ↔ classe
affectations     → rattachement enseignant ↔ matière ↔ classe
notes            → les notes obtenues par étudiant par matière
```

### 2.2 Script SQL de création de la base de données

Ouvrez **phpMyAdmin**, créez une base nommée `gestion_scolaire`, puis exécutez le script suivant dans l'onglet **SQL** :

```sql
-- ============================================================
-- CRÉATION DE LA BASE DE DONNÉES
-- ============================================================
CREATE DATABASE IF NOT EXISTS gestion_scolaire
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_scolaire;

-- ============================================================
-- TABLE DES UTILISATEURS (comptes de connexion)
-- ============================================================
-- Chaque personne ayant accès à l'application a un compte ici.
-- Le champ 'role' détermine ce qu'elle peut faire.
-- Le mot de passe est stocké HACHÉ (jamais en clair).
-- ============================================================
CREATE TABLE utilisateurs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    prenom      VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,       -- stocké haché avec password_hash()
    role        ENUM('admin','enseignant','etudiant') NOT NULL DEFAULT 'etudiant',
    actif       TINYINT(1) NOT NULL DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE DES CLASSES
-- ============================================================
-- Une classe regroupe des étudiants pour une année donnée.
-- ============================================================
CREATE TABLE classes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(50) NOT NULL,          -- ex. : L2-Info-A
    niveau      VARCHAR(20) NOT NULL,          -- ex. : L2, L3, M1
    annee_scolaire VARCHAR(9) NOT NULL,        -- ex. : 2024-2025
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- TABLE DES ÉTUDIANTS
-- ============================================================
CREATE TABLE etudiants (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    matricule       VARCHAR(20) NOT NULL UNIQUE,  -- numéro d'identification unique
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    date_naissance  DATE,
    sexe            ENUM('M','F') NOT NULL,
    adresse         TEXT,
    telephone       VARCHAR(20),
    email           VARCHAR(150),
    classe_id       INT,                          -- classe actuelle de l'étudiant
    utilisateur_id  INT,                          -- lien vers le compte de connexion
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (classe_id)      REFERENCES classes(id)      ON DELETE SET NULL,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE DES ENSEIGNANTS
-- ============================================================
CREATE TABLE enseignants (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    prenom          VARCHAR(100) NOT NULL,
    specialite      VARCHAR(100),
    telephone       VARCHAR(20),
    email           VARCHAR(150),
    utilisateur_id  INT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE DES MATIÈRES
-- ============================================================
-- Le coefficient est utilisé pour pondérer la note dans le
-- calcul de la moyenne générale.
-- ============================================================
CREATE TABLE matieres (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nom             VARCHAR(100) NOT NULL,
    code            VARCHAR(20) NOT NULL UNIQUE,  -- ex. : INF201
    coefficient     DECIMAL(4,2) NOT NULL DEFAULT 1.00,
    volume_horaire  INT,                           -- en heures
    classe_id       INT NOT NULL,
    enseignant_id   INT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (classe_id)    REFERENCES classes(id)    ON DELETE CASCADE,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id) ON DELETE SET NULL
);

-- ============================================================
-- TABLE DES NOTES
-- ============================================================
-- Une note appartient à un étudiant, pour une matière donnée,
-- lors d'une évaluation (ex. : DS1, Examen Final).
-- ============================================================
CREATE TABLE notes (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    etudiant_id     INT NOT NULL,
    matiere_id      INT NOT NULL,
    type_evaluation VARCHAR(50) NOT NULL,   -- ex. : 'DS1', 'Devoir', 'Examen'
    note            DECIMAL(5,2) NOT NULL,  -- note sur 20
    date_evaluation DATE NOT NULL,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES etudiants(id) ON DELETE CASCADE,
    FOREIGN KEY (matiere_id)  REFERENCES matieres(id)  ON DELETE CASCADE
);

-- ============================================================
-- DONNÉES DE TEST
-- ============================================================

-- Compte administrateur (mot de passe : Admin1234)
-- Le hash ci-dessous est généré avec password_hash('Admin1234', PASSWORD_DEFAULT)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Admin', 'Système', 'admin@isi.sn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Note : ce hash correspond au mot de passe "password" (exemple de test)
-- En production, générez votre propre hash avec : echo password_hash('VotreMotDePasse', PASSWORD_DEFAULT);

-- Deux classes
INSERT INTO classes (nom, niveau, annee_scolaire) VALUES
('L2-Info-A', 'L2', '2024-2025'),
('L2-Info-B', 'L2', '2024-2025');

-- Un enseignant
INSERT INTO enseignants (nom, prenom, specialite, email) VALUES
('Diallo', 'Moussa', 'Développement Web', 'mdiallo@isi.sn'),
('Fall', 'Fatou', 'Base de Données', 'ffall@isi.sn');

-- Des matières pour la classe L2-Info-A (id=1)
INSERT INTO matieres (nom, code, coefficient, volume_horaire, classe_id, enseignant_id) VALUES
('Développement Web PHP',     'INF201', 3.00, 45, 1, 1),
('Base de Données Avancées',  'INF202', 2.00, 30, 1, 2),
('Algorithmique',             'INF203', 2.00, 30, 1, NULL),
('Systèmes d\'exploitation',  'INF204', 1.50, 20, 1, NULL),
('Anglais Technique',         'ANG201', 1.00, 20, 1, NULL);

-- Des étudiants dans la classe L2-Info-A
INSERT INTO etudiants (matricule, nom, prenom, date_naissance, sexe, telephone, email, classe_id) VALUES
('2024001', 'Ndiaye',    'Amadou',   '2002-03-15', 'M', '771234567', 'andiaye@etu.isi.sn',   1),
('2024002', 'Sow',       'Mariama',  '2001-07-22', 'F', '772345678', 'msow@etu.isi.sn',      1),
('2024003', 'Sarr',      'Ibrahima', '2002-11-08', 'M', '773456789', 'isarr@etu.isi.sn',     1),
('2024004', 'Diop',      'Aïssatou', '2001-05-30', 'F', '774567890', 'adiop@etu.isi.sn',     1),
('2024005', 'Mbaye',     'Omar',     '2003-01-12', 'M', '775678901', 'ombaye@etu.isi.sn',    1);

-- Des notes pour les étudiants
INSERT INTO notes (etudiant_id, matiere_id, type_evaluation, note, date_evaluation) VALUES
-- Amadou Ndiaye
(1, 1, 'Devoir 1',    14.00, '2024-10-15'),
(1, 1, 'Examen',      16.50, '2025-01-20'),
(1, 2, 'Devoir 1',    12.00, '2024-10-18'),
(1, 2, 'Examen',      13.50, '2025-01-22'),
(1, 3, 'Devoir 1',    15.00, '2024-10-20'),
(1, 3, 'Examen',      17.00, '2025-01-24'),
-- Mariama Sow
(2, 1, 'Devoir 1',    11.00, '2024-10-15'),
(2, 1, 'Examen',      13.00, '2025-01-20'),
(2, 2, 'Devoir 1',    16.00, '2024-10-18'),
(2, 2, 'Examen',      15.50, '2025-01-22'),
(2, 3, 'Devoir 1',    10.50, '2024-10-20'),
(2, 3, 'Examen',      12.00, '2025-01-24'),
-- Ibrahima Sarr
(3, 1, 'Devoir 1',    18.00, '2024-10-15'),
(3, 1, 'Examen',      17.50, '2025-01-20'),
(3, 2, 'Devoir 1',    14.00, '2024-10-18'),
(3, 2, 'Examen',      16.00, '2025-01-22'),
(3, 3, 'Devoir 1',    16.50, '2024-10-20'),
(3, 3, 'Examen',      18.00, '2025-01-24');
```

> **Explication du script :**
> - `AUTO_INCREMENT` : MySQL génère automatiquement un identifiant unique pour chaque ligne insérée.
> - `FOREIGN KEY` : une clé étrangère crée un lien entre deux tables et garantit la cohérence des données.
> - `ON DELETE CASCADE` : si un étudiant est supprimé, toutes ses notes sont supprimées avec lui.
> - `ON DELETE SET NULL` : si une classe est supprimée, le champ `classe_id` de l'étudiant devient NULL (l'étudiant n'est pas supprimé).

---

> ### ✅ Point de vérification — Étape 2
> 1. Ouvrez phpMyAdmin et vérifiez que la base `gestion_scolaire` contient bien **8 tables**.
> 2. Consultez la table `etudiants` — combien de lignes contient-elle ?
> 3. Faites une requête SQL manuelle pour lister les matières de la classe `L2-Info-A` avec leur coefficient :
>    ```sql
>    SELECT m.nom, m.coefficient FROM matieres m JOIN classes c ON m.classe_id = c.id WHERE c.nom = 'L2-Info-A';
>    ```
> 4. Quel résultat obtenez-vous ? Comparez avec votre voisin.
>
> ➡️ Si vous avez bien 5 matières dans le résultat, passez à l'étape suivante.

---

## Étape 3 — Structure du projet PHP

### 3.1 Organisation des fichiers

Une bonne structure de fichiers est essentielle. Créez l'arborescence suivante dans le répertoire racine de votre serveur local (`htdocs` pour XAMPP ou `www` pour WAMP) :

```
gestion_scolaire/
│
├── config/
│   └── database.php          ← connexion à la BDD
│
├── includes/
│   ├── header.php            ← en-tête commun à toutes les pages
│   ├── footer.php            ← pied de page commun
│   ├── auth.php              ← fonctions d'authentification
│   └── functions.php         ← fonctions utilitaires
│
├── assets/
│   ├── css/
│   │   └── style.css         ← styles personnalisés
│   └── js/
│       └── script.js         ← scripts JavaScript
│
├── modules/
│   ├── etudiants/
│   │   ├── liste.php
│   │   ├── ajouter.php
│   │   ├── modifier.php
│   │   └── supprimer.php
│   ├── enseignants/
│   │   ├── liste.php
│   │   └── ajouter.php
│   ├── classes/
│   │   └── liste.php
│   ├── matieres/
│   │   ├── liste.php
│   │   └── ajouter.php
│   ├── notes/
│   │   ├── saisie.php
│   │   └── liste.php
│   └── bulletins/
│       └── generer.php
│
├── index.php                 ← page d'accueil / redirection
├── login.php                 ← page de connexion
├── logout.php                ← déconnexion
└── dashboard.php             ← tableau de bord après connexion
```

> **Pourquoi cette structure ?**
> - Le dossier `config/` centralise la configuration : si vous changez de base de données, vous ne modifiez qu'un seul fichier.
> - Le dossier `includes/` évite la répétition du code (principe DRY : *Don't Repeat Yourself*).
> - Le dossier `modules/` sépare chaque fonctionnalité dans son propre répertoire.

### 3.2 Création des dossiers

Dans votre terminal (ou via votre explorateur de fichiers), créez la structure :

```bash
mkdir -p gestion_scolaire/{config,includes,assets/{css,js},modules/{etudiants,enseignants,classes,matieres,notes,bulletins}}
```

---

> ### ✅ Point de vérification — Étape 3
> Vérifiez dans votre explorateur de fichiers que la structure existe bien.
> - Le dossier `gestion_scolaire/` doit être dans `htdocs/` (XAMPP) ou `www/` (WAMP).
> - Accédez à `http://localhost/gestion_scolaire/` dans votre navigateur. Vous devez voir soit une page vide, soit une erreur 403 (normal pour l'instant).
>
> ➡️ Si la structure est en place, passez à l'étape suivante.

---

## Étape 4 — Connexion à la base de données

### 4.1 Fichier de configuration — `config/database.php`

Ce fichier est le **cœur de la connexion**. Nous utilisons **PDO** (PHP Data Objects), une interface moderne et sécurisée pour communiquer avec MySQL.

```php
<?php
// =============================================================
// config/database.php
// Ce fichier établit la connexion à la base de données MySQL.
// Il est inclus dans toutes les pages qui ont besoin de la BDD.
// =============================================================

// Paramètres de connexion — adaptez selon votre configuration locale
define('DB_HOST', 'localhost');   // adresse du serveur MySQL
define('DB_NAME', 'gestion_scolaire'); // nom de la base de données
define('DB_USER', 'root');        // nom d'utilisateur MySQL (par défaut : root)
define('DB_PASS', '');            // mot de passe MySQL (vide par défaut sur XAMPP)
define('DB_CHARSET', 'utf8mb4'); // encodage des caractères

// Chaîne DSN (Data Source Name) — format attendu par PDO
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

// Options de connexion PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lève une exception en cas d'erreur
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // désactive l'émulation des requêtes préparées
];

try {
    // Tentative de connexion
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // En cas d'erreur, on affiche un message clair et on arrête le script
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
```

> **Explication détaillée :**
> - `PDO` : classe PHP qui permet de parler à MySQL (et à d'autres systèmes de BDD) de façon unifiée.
> - `PDO::ERRMODE_EXCEPTION` : si une requête échoue, PHP lance une exception — cela vous oblige à gérer les erreurs.
> - `PDO::FETCH_ASSOC` : les résultats des requêtes seront des tableaux associatifs, ex. `$row['nom']` au lieu de `$row[0]`.
> - `PDO::ATTR_EMULATE_PREPARES => false` : force l'utilisation des vraies requêtes préparées, ce qui empêche les injections SQL.

### 4.2 Fichier de fonctions utilitaires — `includes/functions.php`

```php
<?php
// =============================================================
// includes/functions.php
// Fonctions réutilisables dans toute l'application.
// =============================================================

/**
 * Nettoie une chaîne pour éviter les failles XSS.
 * À utiliser AVANT d'afficher toute donnée venant de l'utilisateur ou de la BDD.
 *
 * @param string $data La donnée à nettoyer
 * @return string La donnée nettoyée et sécurisée
 */
function clean(string $data): string {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Calcule la moyenne pondérée d'un étudiant pour une matière donnée.
 * La moyenne pondérée tient compte du coefficient de chaque matière.
 *
 * @param array $notes Tableau de notes avec leurs coefficients
 * @return float La moyenne calculée, ou 0 si pas de notes
 */
function calculerMoyenne(array $notes): float {
    if (empty($notes)) return 0.0;

    $totalPoints     = 0; // somme des (note × coefficient)
    $totalCoeff      = 0; // somme des coefficients

    foreach ($notes as $note) {
        $totalPoints += $note['note'] * $note['coefficient'];
        $totalCoeff  += $note['coefficient'];
    }

    return $totalCoeff > 0 ? round($totalPoints / $totalCoeff, 2) : 0.0;
}

/**
 * Retourne la mention correspondant à une moyenne.
 *
 * @param float $moyenne La moyenne sur 20
 * @return string La mention textuelle
 */
function getMention(float $moyenne): string {
    if ($moyenne >= 16)      return 'Très Bien';
    elseif ($moyenne >= 14)  return 'Bien';
    elseif ($moyenne >= 12)  return 'Assez Bien';
    elseif ($moyenne >= 10)  return 'Passable';
    else                     return 'Insuffisant';
}

/**
 * Retourne la classe CSS Bootstrap correspondant à la mention.
 * Utilisé pour coloriser les notes dans le bulletin.
 */
function getCouleurMention(float $moyenne): string {
    if ($moyenne >= 16)      return 'success';
    elseif ($moyenne >= 14)  return 'primary';
    elseif ($moyenne >= 12)  return 'info';
    elseif ($moyenne >= 10)  return 'warning';
    else                     return 'danger';
}

/**
 * Formate une date de la forme YYYY-MM-DD vers DD/MM/YYYY.
 */
function formatDate(?string $date): string {
    if (!$date) return '—';
    return date('d/m/Y', strtotime($date));
}

/**
 * Redirige vers une URL donnée.
 */
function redirect(string $url): void {
    header("Location: $url");
    exit();
}
```

---

> ### ✅ Point de vérification — Étape 4
> Créez un fichier temporaire `gestion_scolaire/test_connexion.php` avec le contenu suivant :
> ```php
> <?php
> require_once 'config/database.php';
> echo "✅ Connexion réussie à la base : " . DB_NAME;
> ```
> Accédez à `http://localhost/gestion_scolaire/test_connexion.php`.
> - ✅ Si vous voyez le message de succès → la connexion fonctionne.
> - ❌ Si vous avez une erreur → vérifiez les paramètres dans `config/database.php` (user, password, nom de BDD).
>
> **Supprimez ce fichier de test** avant de continuer (ne jamais laisser des fichiers de test en production).

---

## Étape 5 — Gabarits communs (header / footer)

### 5.1 En-tête — `includes/header.php`

Ce fichier sera inclus en haut de chaque page. Il charge Bootstrap et affiche la barre de navigation.

```php
<?php
// =============================================================
// includes/header.php
// En-tête HTML commun à toutes les pages de l'application.
// Ce fichier doit être inclus en tout premier dans chaque page.
// =============================================================

// On s'assure que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Variable pour le titre de la page (peut être définie avant l'include)
$pageTitle = $pageTitle ?? 'Gestion Scolaire — ISI';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= clean($pageTitle) ?></title>

    <!-- Bootstrap 5 CSS (chargé depuis un CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Style personnalisé -->
    <link href="/gestion_scolaire/assets/css/style.css" rel="stylesheet">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <!-- Logo / Nom de l'application -->
        <a class="navbar-brand fw-bold" href="/gestion_scolaire/dashboard.php">
            <i class="bi bi-mortarboard-fill me-2"></i>GestionScolaire ISI
        </a>

        <!-- Bouton hamburger pour mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Liens de navigation — affichés seulement si connecté -->
        <?php if (isset($_SESSION['utilisateur_id'])): ?>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/gestion_scolaire/dashboard.php">
                        <i class="bi bi-speedometer2"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-people-fill"></i> Étudiants
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/etudiants/liste.php">Liste des étudiants</a></li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/etudiants/ajouter.php">Ajouter un étudiant</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-badge"></i> Enseignants
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/enseignants/liste.php">Liste des enseignants</a></li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/enseignants/ajouter.php">Ajouter un enseignant</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-journal-text"></i> Académique
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/classes/liste.php">Classes</a></li>
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/matieres/liste.php">Matières</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/notes/saisie.php">Saisie des notes</a></li>
                        <li><a class="dropdown-item" href="/gestion_scolaire/modules/bulletins/generer.php">Bulletins</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Informations utilisateur connecté -->
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= clean($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>
                        <span class="badge bg-warning text-dark ms-1">
                            <?= clean($_SESSION['role']) ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item text-danger" href="/gestion_scolaire/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Déconnexion
                        </a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</nav>

<!-- Zone de messages flash (succès / erreur) -->
<?php if (isset($_SESSION['message'])): ?>
<div class="container-fluid mt-2">
    <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
        <i class="bi bi-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
        <?= clean($_SESSION['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>
<?php
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
endif;
?>

<!-- Contenu principal de la page -->
<main class="container-fluid py-4">
```

### 5.2 Pied de page — `includes/footer.php`

```php
<?php
// =============================================================
// includes/footer.php
// Ferme les balises HTML ouvertes dans header.php.
// =============================================================
?>
</main>

<footer class="bg-dark text-white text-center py-3 mt-4">
    <small>
        &copy; <?= date('Y') ?> GestionScolaire ISI — Tous droits réservés
    </small>
</footer>

<!-- Bootstrap 5 JavaScript (nécessaire pour les menus déroulants et les alertes) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/gestion_scolaire/assets/js/script.js"></script>
</body>
</html>
```

### 5.3 Styles personnalisés — `assets/css/style.css`

```css
/* =============================================================
   assets/css/style.css
   Styles personnalisés complémentaires à Bootstrap.
   ============================================================= */

/* Corps de la page */
body {
    background-color: #f8f9fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Cartes de statistiques sur le tableau de bord */
.stat-card {
    border-radius: 12px;
    border: none;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-card .stat-icon {
    font-size: 2.5rem;
    opacity: 0.85;
}

/* Tableau de données */
.table thead {
    background-color: #0d6efd;
    color: white;
}

.table-hover tbody tr:hover {
    background-color: #e8f0fe;
}

/* Formulaires */
.form-section {
    background: white;
    border-radius: 10px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
}

/* Bulletin de notes */
.bulletin-header {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    color: white;
    border-radius: 10px 10px 0 0;
    padding: 1.5rem;
}

.bulletin-table th {
    background-color: #e9ecef;
}

/* Badges de mention */
.badge-mention {
    font-size: 0.85rem;
    padding: 0.4em 0.75em;
    border-radius: 20px;
}

/* Navigation active */
.navbar-nav .nav-link.active {
    font-weight: bold;
    text-decoration: underline;
}
```

---

## Étape 6 — Authentification

### 6.1 Fonctions d'authentification — `includes/auth.php`

```php
<?php
// =============================================================
// includes/auth.php
// Gestion de l'authentification et des sessions.
// =============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Vérifie si l'utilisateur est connecté.
 * Redirige vers la page de connexion si ce n'est pas le cas.
 */
function requireLogin(): void {
    if (!isset($_SESSION['utilisateur_id'])) {
        // On mémorise la page demandée pour y revenir après connexion
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect('/gestion_scolaire/login.php');
    }
}

/**
 * Vérifie si l'utilisateur a le rôle requis.
 * Affiche une erreur 403 si le rôle est insuffisant.
 *
 * @param string $roleRequis Le rôle minimum requis ('admin', 'enseignant')
 */
function requireRole(string $roleRequis): void {
    requireLogin(); // doit d'abord être connecté

    $rolesHierarchie = ['etudiant' => 1, 'enseignant' => 2, 'admin' => 3];
    $niveauActuel  = $rolesHierarchie[$_SESSION['role']] ?? 0;
    $niveauRequis  = $rolesHierarchie[$roleRequis] ?? 99;

    if ($niveauActuel < $niveauRequis) {
        http_response_code(403);
        die('
            <div class="container mt-5 text-center">
                <h1 class="text-danger">⛔ Accès refusé</h1>
                <p>Vous n\'avez pas les droits suffisants pour accéder à cette page.</p>
                <a href="/gestion_scolaire/dashboard.php" class="btn btn-primary">Retour au tableau de bord</a>
            </div>
        ');
    }
}

/**
 * Connecte un utilisateur : vérifie les identifiants et crée la session.
 *
 * @param PDO    $pdo      L'objet de connexion à la BDD
 * @param string $email    Email saisi dans le formulaire
 * @param string $password Mot de passe en clair saisi dans le formulaire
 * @return bool true si la connexion réussit, false sinon
 */
function login(PDO $pdo, string $email, string $password): bool {
    // Requête préparée : jamais de données utilisateur directement dans le SQL
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, email, mot_de_passe, role
        FROM utilisateurs
        WHERE email = :email AND actif = 1
    ");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch();

    // password_verify() compare le mot de passe en clair avec son hash stocké en BDD
    if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
        // Régénérer l'ID de session pour prévenir la fixation de session
        session_regenerate_id(true);

        // Stocker les informations utiles dans la session
        $_SESSION['utilisateur_id'] = $utilisateur['id'];
        $_SESSION['nom']            = $utilisateur['nom'];
        $_SESSION['prenom']         = $utilisateur['prenom'];
        $_SESSION['email']          = $utilisateur['email'];
        $_SESSION['role']           = $utilisateur['role'];

        return true;
    }

    return false; // identifiants incorrects
}

/**
 * Déconnecte l'utilisateur : détruit la session.
 */
function logout(): void {
    session_unset();
    session_destroy();
    redirect('/gestion_scolaire/login.php');
}
```

### 6.2 Page de connexion — `login.php`

```php
<?php
// =============================================================
// login.php
// Page de connexion à l'application.
// =============================================================

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

// Si l'utilisateur est déjà connecté, on le redirige vers le dashboard
if (isset($_SESSION['utilisateur_id'])) {
    redirect('/gestion_scolaire/dashboard.php');
}

$erreur = ''; // message d'erreur à afficher

// Traitement du formulaire quand il est soumis (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } elseif (login($pdo, $email, $password)) {
        // Connexion réussie → redirection vers le dashboard (ou page demandée)
        $redirect = $_SESSION['redirect_after_login'] ?? '/gestion_scolaire/dashboard.php';
        unset($_SESSION['redirect_after_login']);
        redirect($redirect);
    } else {
        $erreur = 'Email ou mot de passe incorrect.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — GestionScolaire ISI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/gestion_scolaire/assets/css/style.css" rel="stylesheet">
    <style>
        /* Style spécifique à la page de connexion */
        body { background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card">
                <div class="card-body p-4">
                    <!-- En-tête du formulaire -->
                    <div class="text-center mb-4">
                        <i class="bi bi-mortarboard-fill text-primary" style="font-size: 3rem;"></i>
                        <h3 class="mt-2 fw-bold">GestionScolaire</h3>
                        <p class="text-muted">Institut Supérieur d'Informatique</p>
                    </div>

                    <!-- Message d'erreur -->
                    <?php if ($erreur): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= clean($erreur) ?>
                    </div>
                    <?php endif; ?>

                    <!-- Formulaire de connexion -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Adresse email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="votre@email.sn"
                                       value="<?= clean($_POST['email'] ?? '') ?>"
                                       required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Mot de passe</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Votre mot de passe" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                        </button>
                    </form>

                    <hr>
                    <div class="text-center text-muted small">
                        <strong>Compte de test :</strong><br>
                        Email : admin@isi.sn<br>
                        Mot de passe : <em>(celui que vous avez hashé)</em>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

> **Important — Créer le hash du mot de passe admin :**
> Créez temporairement un fichier `gestion_scolaire/generate_hash.php` :
> ```php
> <?php
> echo password_hash('Admin1234', PASSWORD_DEFAULT);
> ```
> Copiez le résultat, puis mettez à jour la table `utilisateurs` :
> ```sql
> UPDATE utilisateurs SET mot_de_passe = 'COLLEZ_LE_HASH_ICI' WHERE email = 'admin@isi.sn';
> ```
> **Supprimez `generate_hash.php` immédiatement après.**

### 6.3 Déconnexion — `logout.php`

```php
<?php
// =============================================================
// logout.php
// Déconnecte l'utilisateur et le redirige vers la page de connexion.
// =============================================================

require_once 'includes/auth.php';
logout(); // cette fonction détruit la session et redirige
```

---

> ### ✅ Point de vérification — Étape 6
> 1. Accédez à `http://localhost/gestion_scolaire/login.php`
> 2. Essayez de vous connecter avec de mauvais identifiants → vous devez voir le message d'erreur.
> 3. Connectez-vous avec `admin@isi.sn` et votre mot de passe → vous devez être redirigé vers `dashboard.php` (page encore vide).
> 4. Accédez directement à `http://localhost/gestion_scolaire/dashboard.php` sans être connecté → vous devez être redirigé vers `login.php`.
>
> ➡️ Si les 4 tests passent, l'authentification est fonctionnelle. Passez à l'étape suivante.

---

## Étape 7 — Tableau de bord

### `dashboard.php`

```php
<?php
// =============================================================
// dashboard.php
// Tableau de bord principal affiché après la connexion.
// Affiche des statistiques générales sur l'application.
// =============================================================

require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

requireLogin(); // ← redirige vers login.php si non connecté

$pageTitle = 'Tableau de bord — GestionScolaire';

// -----------------------------------------------------------
// Récupération des statistiques depuis la BDD
// Chaque requête retourne un COUNT(*) pour afficher les totaux.
// -----------------------------------------------------------

$stats = [];

// Nombre total d'étudiants
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM etudiants");
$stats['etudiants'] = $stmt->fetch()['total'];

// Nombre total d'enseignants
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM enseignants");
$stats['enseignants'] = $stmt->fetch()['total'];

// Nombre total de classes
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM classes");
$stats['classes'] = $stmt->fetch()['total'];

// Nombre total de matières
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM matieres");
$stats['matieres'] = $stmt->fetch()['total'];

// Nombre total de notes saisies
$stmt = $pdo->query("SELECT COUNT(*) AS total FROM notes");
$stats['notes'] = $stmt->fetch()['total'];

// Liste des 5 derniers étudiants inscrits
$stmt = $pdo->query("
    SELECT e.nom, e.prenom, e.matricule, c.nom AS classe, e.created_at
    FROM etudiants e
    LEFT JOIN classes c ON e.classe_id = c.id
    ORDER BY e.created_at DESC
    LIMIT 5
");
$derniersEtudiants = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">
            <i class="bi bi-speedometer2 text-primary me-2"></i>Tableau de bord
        </h2>
        <p class="text-muted mb-0">
            Bonjour, <strong><?= clean($_SESSION['prenom']) ?></strong> !
            Nous sommes le <?= date('d/m/Y') ?>.
        </p>
    </div>
</div>

<!-- Cartes de statistiques -->
<div class="row g-4 mb-4">

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card stat-card bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fs-1 fw-bold"><?= $stats['etudiants'] ?></div>
                    <div class="text-white-50">Étudiants</div>
                </div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/etudiants/liste.php" class="text-white text-decoration-none small">
                    Voir la liste <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card stat-card bg-success text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fs-1 fw-bold"><?= $stats['enseignants'] ?></div>
                    <div class="text-white-50">Enseignants</div>
                </div>
                <i class="bi bi-person-badge-fill stat-icon"></i>
            </div>
            <div class="card-footer bg-transparent border-0">
                <a href="modules/enseignants/liste.php" class="text-white text-decoration-none small">
                    Voir la liste <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card stat-card bg-info text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fs-1 fw-bold"><?= $stats['classes'] ?></div>
                    <div class="text-white-50">Classes</div>
                </div>
                <i class="bi bi-building stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card stat-card bg-warning text-dark h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fs-1 fw-bold"><?= $stats['matieres'] ?></div>
                    <div class="text-white-50">Matières</div>
                </div>
                <i class="bi bi-book-fill stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card stat-card bg-danger text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fs-1 fw-bold"><?= $stats['notes'] ?></div>
                    <div class="text-white-50">Notes saisies</div>
                </div>
                <i class="bi bi-clipboard-data-fill stat-icon"></i>
            </div>
        </div>
    </div>

</div>

<!-- Tableau des derniers étudiants inscrits -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history text-primary me-2"></i>Dernières inscriptions
                </h5>
                <a href="modules/etudiants/liste.php" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th>Classe</th>
                            <th>Date d'inscription</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($derniersEtudiants as $etudiant): ?>
                        <tr>
                            <td><code><?= clean($etudiant['matricule']) ?></code></td>
                            <td><?= clean($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></td>
                            <td>
                                <span class="badge bg-primary"><?= clean($etudiant['classe'] ?? '—') ?></span>
                            </td>
                            <td><?= formatDate($etudiant['created_at']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($derniersEtudiants)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-3">Aucun étudiant enregistré</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Raccourcis d'actions -->
    <div class="col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold"><i class="bi bi-lightning-fill text-warning me-2"></i>Actions rapides</h5>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="modules/etudiants/ajouter.php" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-2"></i>Nouvel étudiant
                </a>
                <a href="modules/enseignants/ajouter.php" class="btn btn-outline-success">
                    <i class="bi bi-person-badge me-2"></i>Nouvel enseignant
                </a>
                <a href="modules/notes/saisie.php" class="btn btn-outline-warning">
                    <i class="bi bi-pencil-square me-2"></i>Saisir des notes
                </a>
                <a href="modules/bulletins/generer.php" class="btn btn-outline-info">
                    <i class="bi bi-file-earmark-text me-2"></i>Générer un bulletin
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
```

---

## Étape 8 — Module Étudiants (CRUD complet)

### 8.1 Liste des étudiants — `modules/etudiants/liste.php`

```php
<?php
// =============================================================
// modules/etudiants/liste.php
// Affiche la liste de tous les étudiants avec recherche et filtre.
// CRUD = Create, Read, Update, Delete
// Cette page gère le R (Read) du CRUD.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$pageTitle = 'Liste des étudiants';

// ---------------------------------------------------------------
// Recherche et filtrage
// $_GET récupère les paramètres passés dans l'URL (ex: ?recherche=Ndiaye&classe=1)
// ---------------------------------------------------------------
$recherche  = trim($_GET['recherche'] ?? '');
$filtreClasse = (int)($_GET['classe_id'] ?? 0);

// Construction de la requête SQL dynamiquement selon les filtres
// La jointure LEFT JOIN permet d'inclure les étudiants sans classe assignée
$sql = "
    SELECT e.id, e.matricule, e.nom, e.prenom, e.sexe, e.telephone, e.email, c.nom AS classe
    FROM etudiants e
    LEFT JOIN classes c ON e.classe_id = c.id
    WHERE 1=1
";

$params = [];

// Ajout du filtre de recherche si renseigné
if (!empty($recherche)) {
    $sql .= " AND (e.nom LIKE :recherche OR e.prenom LIKE :recherche OR e.matricule LIKE :recherche)";
    $params[':recherche'] = "%$recherche%"; // % = n'importe quels caractères
}

// Ajout du filtre de classe si sélectionné
if ($filtreClasse > 0) {
    $sql .= " AND e.classe_id = :classe_id";
    $params[':classe_id'] = $filtreClasse;
}

$sql .= " ORDER BY e.nom, e.prenom";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$etudiants = $stmt->fetchAll();

// Récupération des classes pour le menu déroulant de filtre
$classes = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-people-fill text-primary me-2"></i>Étudiants</h2>
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="ajouter.php" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Ajouter un étudiant
    </a>
    <?php endif; ?>
</div>

<!-- Formulaire de recherche et filtre -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Recherche</label>
                <input type="text" class="form-control" name="recherche"
                       placeholder="Nom, prénom ou matricule..."
                       value="<?= clean($recherche) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Filtrer par classe</label>
                <select class="form-select" name="classe_id">
                    <option value="0">— Toutes les classes —</option>
                    <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>"
                        <?= $filtreClasse === (int)$classe['id'] ? 'selected' : '' ?>>
                        <?= clean($classe['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-fill">
                    <i class="bi bi-search me-1"></i>Rechercher
                </button>
                <a href="liste.php" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Résultats -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <span class="text-muted">
            <strong><?= count($etudiants) ?></strong> étudiant(s) trouvé(s)
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Matricule</th>
                    <th>Nom complet</th>
                    <th>Sexe</th>
                    <th>Classe</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $e): ?>
                <tr>
                    <td><code><?= clean($e['matricule']) ?></code></td>
                    <td class="fw-semibold"><?= clean($e['nom'] . ' ' . $e['prenom']) ?></td>
                    <td>
                        <span class="badge bg-<?= $e['sexe'] === 'M' ? 'primary' : 'danger' ?>">
                            <?= $e['sexe'] === 'M' ? 'Masculin' : 'Féminin' ?>
                        </span>
                    </td>
                    <td><?= clean($e['classe'] ?? '—') ?></td>
                    <td><?= clean($e['telephone'] ?? '—') ?></td>
                    <td><?= clean($e['email'] ?? '—') ?></td>
                    <td class="text-center">
                        <a href="modifier.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-primary me-1"
                           title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="supprimer.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline-danger"
                           title="Supprimer"
                           onclick="return confirm('Supprimer cet étudiant ? Cette action est irréversible.')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($etudiants)): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-search fs-2 d-block mb-2"></i>
                        Aucun étudiant trouvé.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
```

### 8.2 Ajouter un étudiant — `modules/etudiants/ajouter.php`

```php
<?php
// =============================================================
// modules/etudiants/ajouter.php
// Formulaire d'ajout d'un nouvel étudiant.
// Gère le C (Create) du CRUD.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireRole('admin'); // seul l'admin peut ajouter des étudiants

$pageTitle  = 'Ajouter un étudiant';
$erreurs    = [];
$succes     = false;

// Récupération des classes disponibles pour le menu déroulant
$classes = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

// ---------------------------------------------------------------
// Traitement du formulaire (quand soumis en POST)
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Récupération et nettoyage des données du formulaire
    $matricule      = trim($_POST['matricule']      ?? '');
    $nom            = trim($_POST['nom']            ?? '');
    $prenom         = trim($_POST['prenom']         ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $sexe           = trim($_POST['sexe']           ?? '');
    $adresse        = trim($_POST['adresse']        ?? '');
    $telephone      = trim($_POST['telephone']      ?? '');
    $email          = trim($_POST['email']          ?? '');
    $classe_id      = (int)($_POST['classe_id']     ?? 0);

    // 2. Validation des données obligatoires
    if (empty($matricule))  $erreurs[] = 'Le matricule est obligatoire.';
    if (empty($nom))        $erreurs[] = 'Le nom est obligatoire.';
    if (empty($prenom))     $erreurs[] = 'Le prénom est obligatoire.';
    if (!in_array($sexe, ['M', 'F'])) $erreurs[] = 'Le sexe est obligatoire.';

    // 3. Vérification de l'unicité du matricule
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("SELECT id FROM etudiants WHERE matricule = :matricule");
        $stmt->execute([':matricule' => $matricule]);
        if ($stmt->fetch()) {
            $erreurs[] = "Le matricule « $matricule » est déjà utilisé.";
        }
    }

    // 4. Insertion en base si tout est valide
    if (empty($erreurs)) {
        $stmt = $pdo->prepare("
            INSERT INTO etudiants
                (matricule, nom, prenom, date_naissance, sexe, adresse, telephone, email, classe_id)
            VALUES
                (:matricule, :nom, :prenom, :date_naissance, :sexe, :adresse, :telephone, :email, :classe_id)
        ");

        $stmt->execute([
            ':matricule'       => $matricule,
            ':nom'             => $nom,
            ':prenom'          => $prenom,
            ':date_naissance'  => $date_naissance ?: null, // null si vide
            ':sexe'            => $sexe,
            ':adresse'         => $adresse ?: null,
            ':telephone'       => $telephone ?: null,
            ':email'           => $email ?: null,
            ':classe_id'       => $classe_id > 0 ? $classe_id : null,
        ]);

        // Message de succès via la session (persistera après la redirection)
        $_SESSION['message']      = "L'étudiant $prenom $nom a bien été ajouté.";
        $_SESSION['message_type'] = 'success';

        redirect('/gestion_scolaire/modules/etudiants/liste.php');
    }
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i>Ajouter un étudiant</h2>
    <a href="liste.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour à la liste
    </a>
</div>

<!-- Affichage des erreurs de validation -->
<?php if (!empty($erreurs)): ?>
<div class="alert alert-danger">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <strong>Veuillez corriger les erreurs suivantes :</strong>
    <ul class="mb-0 mt-2">
        <?php foreach ($erreurs as $erreur): ?>
        <li><?= clean($erreur) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<!-- Formulaire d'ajout -->
<div class="form-section">
    <form method="POST" action="">
        <div class="row g-3">
            <!-- Informations d'identification -->
            <div class="col-12">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-person me-2"></i>Informations d'identification
                </h5>
            </div>

            <div class="col-md-4">
                <label for="matricule" class="form-label fw-semibold">
                    Matricule <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="matricule" name="matricule"
                       placeholder="ex. 2024006" required
                       value="<?= clean($_POST['matricule'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label for="nom" class="form-label fw-semibold">
                    Nom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="nom" name="nom"
                       placeholder="Nom de famille" required
                       value="<?= clean($_POST['nom'] ?? '') ?>">
            </div>

            <div class="col-md-4">
                <label for="prenom" class="form-label fw-semibold">
                    Prénom <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control" id="prenom" name="prenom"
                       placeholder="Prénom" required
                       value="<?= clean($_POST['prenom'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label for="date_naissance" class="form-label fw-semibold">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance"
                       value="<?= clean($_POST['date_naissance'] ?? '') ?>">
            </div>

            <div class="col-md-3">
                <label for="sexe" class="form-label fw-semibold">
                    Sexe <span class="text-danger">*</span>
                </label>
                <select class="form-select" id="sexe" name="sexe" required>
                    <option value="">— Choisir —</option>
                    <option value="M" <?= ($_POST['sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= ($_POST['sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>

            <div class="col-md-6">
                <label for="classe_id" class="form-label fw-semibold">Classe</label>
                <select class="form-select" id="classe_id" name="classe_id">
                    <option value="0">— Non affecté —</option>
                    <?php foreach ($classes as $classe): ?>
                    <option value="<?= $classe['id'] ?>"
                        <?= ($_POST['classe_id'] ?? 0) == $classe['id'] ? 'selected' : '' ?>>
                        <?= clean($classe['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Coordonnées -->
            <div class="col-12 mt-3">
                <h5 class="text-primary border-bottom pb-2">
                    <i class="bi bi-telephone me-2"></i>Coordonnées
                </h5>
            </div>

            <div class="col-md-6">
                <label for="telephone" class="form-label fw-semibold">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone"
                       placeholder="ex. 77 123 45 67"
                       value="<?= clean($_POST['telephone'] ?? '') ?>">
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" id="email" name="email"
                       placeholder="etudiant@email.com"
                       value="<?= clean($_POST['email'] ?? '') ?>">
            </div>

            <div class="col-12">
                <label for="adresse" class="form-label fw-semibold">Adresse</label>
                <textarea class="form-control" id="adresse" name="adresse" rows="2"
                          placeholder="Quartier, Ville..."><?= clean($_POST['adresse'] ?? '') ?></textarea>
            </div>

            <!-- Boutons -->
            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-2"></i>Enregistrer l'étudiant
                </button>
                <a href="liste.php" class="btn btn-outline-secondary px-4">Annuler</a>
            </div>
        </div>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
```

### 8.3 Modifier un étudiant — `modules/etudiants/modifier.php`

```php
<?php
// =============================================================
// modules/etudiants/modifier.php
// Formulaire de modification d'un étudiant existant.
// Gère le U (Update) du CRUD.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$pageTitle = 'Modifier un étudiant';
$erreurs   = [];

// Récupération de l'ID passé en paramètre URL (?id=5)
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect('/gestion_scolaire/modules/etudiants/liste.php');
}

// Chargement des données actuelles de l'étudiant
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id = :id");
$stmt->execute([':id' => $id]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    $_SESSION['message']      = "Étudiant introuvable.";
    $_SESSION['message_type'] = 'danger';
    redirect('/gestion_scolaire/modules/etudiants/liste.php');
}

$classes = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom            = trim($_POST['nom']            ?? '');
    $prenom         = trim($_POST['prenom']         ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $sexe           = trim($_POST['sexe']           ?? '');
    $adresse        = trim($_POST['adresse']        ?? '');
    $telephone      = trim($_POST['telephone']      ?? '');
    $email          = trim($_POST['email']          ?? '');
    $classe_id      = (int)($_POST['classe_id']     ?? 0);

    if (empty($nom))    $erreurs[] = 'Le nom est obligatoire.';
    if (empty($prenom)) $erreurs[] = 'Le prénom est obligatoire.';

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("
            UPDATE etudiants SET
                nom = :nom, prenom = :prenom, date_naissance = :dn,
                sexe = :sexe, adresse = :adresse, telephone = :tel,
                email = :email, classe_id = :classe_id
            WHERE id = :id
        ");
        $stmt->execute([
            ':nom'      => $nom,
            ':prenom'   => $prenom,
            ':dn'       => $date_naissance ?: null,
            ':sexe'     => $sexe,
            ':adresse'  => $adresse ?: null,
            ':tel'      => $telephone ?: null,
            ':email'    => $email ?: null,
            ':classe_id'=> $classe_id > 0 ? $classe_id : null,
            ':id'       => $id,
        ]);

        $_SESSION['message']      = "Les informations de $prenom $nom ont été mises à jour.";
        $_SESSION['message_type'] = 'success';
        redirect('/gestion_scolaire/modules/etudiants/liste.php');
    }

    // En cas d'erreur, on pré-remplit avec les données soumises
    $etudiant = array_merge($etudiant, $_POST);
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">
        <i class="bi bi-pencil-square text-primary me-2"></i>
        Modifier — <?= clean($etudiant['prenom'] . ' ' . $etudiant['nom']) ?>
    </h2>
    <a href="liste.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

<?php if (!empty($erreurs)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($erreurs as $e): ?><li><?= clean($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="form-section">
    <form method="POST" action="">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nom" required
                       value="<?= clean($etudiant['nom']) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="prenom" required
                       value="<?= clean($etudiant['prenom']) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date de naissance</label>
                <input type="date" class="form-control" name="date_naissance"
                       value="<?= clean($etudiant['date_naissance'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Sexe</label>
                <select class="form-select" name="sexe">
                    <option value="M" <?= $etudiant['sexe'] === 'M' ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= $etudiant['sexe'] === 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Classe</label>
                <select class="form-select" name="classe_id">
                    <option value="0">— Non affecté —</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>"
                        <?= $etudiant['classe_id'] == $c['id'] ? 'selected' : '' ?>>
                        <?= clean($c['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Téléphone</label>
                <input type="tel" class="form-control" name="telephone"
                       value="<?= clean($etudiant['telephone'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" name="email"
                       value="<?= clean($etudiant['email'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Adresse</label>
                <textarea class="form-control" name="adresse" rows="2"><?= clean($etudiant['adresse'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-2"></i>Enregistrer les modifications
                </button>
                <a href="liste.php" class="btn btn-outline-secondary ms-2">Annuler</a>
            </div>
        </div>
    </form>
</div>

<?php require_once '../../includes/footer.php'; ?>
```

### 8.4 Supprimer un étudiant — `modules/etudiants/supprimer.php`

```php
<?php
// =============================================================
// modules/etudiants/supprimer.php
// Supprime un étudiant de la base de données.
// Gère le D (Delete) du CRUD.
// ATTENTION : Grâce à ON DELETE CASCADE, toutes les notes de
// cet étudiant seront également supprimées.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireRole('admin'); // suppression réservée à l'admin

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // On récupère le nom avant suppression pour le message de confirmation
    $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $etudiant = $stmt->fetch();

    if ($etudiant) {
        $stmt = $pdo->prepare("DELETE FROM etudiants WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['message']      = "L'étudiant {$etudiant['prenom']} {$etudiant['nom']} a été supprimé.";
        $_SESSION['message_type'] = 'warning';
    }
}

redirect('/gestion_scolaire/modules/etudiants/liste.php');
```

---

> ### ✅ Point de vérification — Étape 8
> Testez chaque opération CRUD :
> 1. **Affichage** : la liste des étudiants s'affiche correctement (`liste.php`).
> 2. **Recherche** : tapez "Ndiaye" dans le champ de recherche → seul Amadou Ndiaye apparaît.
> 3. **Ajout** : ajoutez un 6ème étudiant de votre choix via le formulaire.
> 4. **Modification** : changez le téléphone d'un étudiant.
> 5. **Suppression** : supprimez l'étudiant que vous venez d'ajouter.
>
> ➡️ Si les 5 opérations fonctionnent sans erreur PHP, le module Étudiants est complet.

---

## Étape 9 — Module Notes (Saisie)

### `modules/notes/saisie.php`

```php
<?php
// =============================================================
// modules/notes/saisie.php
// Interface de saisie des notes par un enseignant ou un admin.
// L'utilisateur choisit une classe, une matière et une évaluation,
// puis saisit les notes pour chaque étudiant de la classe.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$pageTitle = 'Saisie des notes';
$message   = '';
$erreurs   = [];

// Étape 1 : sélection de la classe et de la matière
$classes = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

$classeId  = (int)($_POST['classe_id'] ?? $_GET['classe_id'] ?? 0);
$matiereId = (int)($_POST['matiere_id'] ?? 0);
$typeEval  = trim($_POST['type_evaluation'] ?? '');
$dateEval  = trim($_POST['date_evaluation'] ?? date('Y-m-d'));

$matieres   = [];
$etudiants  = [];

// Si une classe est sélectionnée, charger ses matières
if ($classeId > 0) {
    $stmt = $pdo->prepare("
        SELECT m.id, m.nom, m.code, m.coefficient
        FROM matieres m
        WHERE m.classe_id = :classe_id
        ORDER BY m.nom
    ");
    $stmt->execute([':classe_id' => $classeId]);
    $matieres = $stmt->fetchAll();
}

// Si une classe ET une matière sont sélectionnées, charger les étudiants
if ($classeId > 0 && $matiereId > 0) {
    $stmt = $pdo->prepare("
        SELECT e.id, e.matricule, e.nom, e.prenom
        FROM etudiants e
        WHERE e.classe_id = :classe_id
        ORDER BY e.nom, e.prenom
    ");
    $stmt->execute([':classe_id' => $classeId]);
    $etudiants = $stmt->fetchAll();
}

// ---------------------------------------------------------------
// Traitement de la soumission des notes
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_notes'])) {

    if (empty($typeEval)) $erreurs[] = "Le type d'évaluation est obligatoire.";
    if (empty($dateEval)) $erreurs[] = "La date est obligatoire.";

    if (empty($erreurs) && !empty($_POST['notes'])) {
        // $_POST['notes'] est un tableau associatif : [etudiant_id => note]
        $insertStmt = $pdo->prepare("
            INSERT INTO notes (etudiant_id, matiere_id, type_evaluation, note, date_evaluation)
            VALUES (:etudiant_id, :matiere_id, :type_eval, :note, :date_eval)
            ON DUPLICATE KEY UPDATE note = :note  -- mise à jour si la note existe déjà
        ");

        $nbInserts = 0;
        foreach ($_POST['notes'] as $etudiantId => $noteValeur) {
            $noteValeur = trim($noteValeur);
            // On ignore les champs vides
            if ($noteValeur === '') continue;

            $note = (float)str_replace(',', '.', $noteValeur); // accepte virgule ou point

            // Validation : la note doit être entre 0 et 20
            if ($note < 0 || $note > 20) {
                $erreurs[] = "La note de l'étudiant ID $etudiantId est invalide (doit être entre 0 et 20).";
                continue;
            }

            $insertStmt->execute([
                ':etudiant_id' => (int)$etudiantId,
                ':matiere_id'  => $matiereId,
                ':type_eval'   => $typeEval,
                ':note'        => $note,
                ':date_eval'   => $dateEval,
            ]);
            $nbInserts++;
        }

        if (empty($erreurs)) {
            $_SESSION['message']      = "$nbInserts note(s) enregistrée(s) avec succès.";
            $_SESSION['message_type'] = 'success';
            redirect("saisie.php?classe_id=$classeId");
        }
    }
}

// Infos de la matière sélectionnée (pour affichage)
$matiereInfo = null;
if ($matiereId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM matieres WHERE id = :id");
    $stmt->execute([':id' => $matiereId]);
    $matiereInfo = $stmt->fetch();
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Saisie des notes</h2>
</div>

<!-- Étape 1 : sélection classe + matière -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        <i class="bi bi-funnel me-2"></i>Étape 1 — Sélectionner la classe et la matière
    </div>
    <div class="card-body">
        <form method="POST" action="" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Classe</label>
                <select class="form-select" name="classe_id" onchange="this.form.submit()">
                    <option value="0">— Choisir une classe —</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classeId == $c['id'] ? 'selected' : '' ?>>
                        <?= clean($c['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($matieres)): ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Matière</label>
                <select class="form-select" name="matiere_id">
                    <option value="0">— Choisir une matière —</option>
                    <?php foreach ($matieres as $m): ?>
                    <option value="<?= $m['id'] ?>" <?= $matiereId == $m['id'] ? 'selected' : '' ?>>
                        <?= clean($m['code'] . ' — ' . $m['nom']) ?> (coef. <?= $m['coefficient'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-right me-1"></i>Charger les étudiants
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Étape 2 : saisie des notes -->
<?php if (!empty($etudiants) && $matiereInfo): ?>

<?php if (!empty($erreurs)): ?>
<div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($erreurs as $e): ?><li><?= clean($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-clipboard2-check me-2"></i>
                Étape 2 — Saisie des notes : <?= clean($matiereInfo['nom']) ?>
                <span class="badge bg-warning text-dark ms-2">Coef. <?= $matiereInfo['coefficient'] ?></span>
            </h5>
            <span class="badge bg-light text-dark"><?= count($etudiants) ?> étudiants</span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="">
            <!-- Champs cachés pour mémoriser la sélection -->
            <input type="hidden" name="classe_id"  value="<?= $classeId ?>">
            <input type="hidden" name="matiere_id" value="<?= $matiereId ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Type d'évaluation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="type_evaluation"
                           placeholder="ex. Devoir 1, Examen Final, Partiel..."
                           value="<?= clean($typeEval) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="date_evaluation"
                           value="<?= clean($dateEval) ?>" required>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Matricule</th>
                            <th>Nom complet</th>
                            <th style="width:150px">Note / 20 <span class="text-danger">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($etudiants as $i => $e): ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td><code><?= clean($e['matricule']) ?></code></td>
                            <td class="fw-semibold"><?= clean($e['nom'] . ' ' . $e['prenom']) ?></td>
                            <td>
                                <!-- name="notes[ID_ETUDIANT]" → PHP reçoit un tableau associatif -->
                                <input type="number" class="form-control text-center fw-bold"
                                       name="notes[<?= $e['id'] ?>]"
                                       min="0" max="20" step="0.25"
                                       placeholder="—">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" name="enregistrer_notes" class="btn btn-success px-4">
                    <i class="bi bi-save me-2"></i>Enregistrer toutes les notes
                </button>
                <a href="saisie.php" class="btn btn-outline-secondary">Recommencer</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
```

---

> ### ✅ Point de vérification — Étape 9
> 1. Accédez à la saisie des notes.
> 2. Sélectionnez la classe `L2-Info-A` → la liste des matières doit apparaître.
> 3. Sélectionnez la matière `Développement Web PHP`, puis chargez les étudiants.
> 4. Saisissez les notes suivantes pour un type d'évaluation "Devoir 2" :
>    - Amadou Ndiaye : 15
>    - Mariama Sow : 12
>    - Ibrahima Sarr : 17
> 5. Cliquez sur "Enregistrer" → vérifiez dans phpMyAdmin que les notes sont bien dans la table `notes`.
>
> ➡️ Si les 3 notes apparaissent en base avec le bon `matiere_id` et `type_evaluation`, passez à l'étape suivante.

---

## Étape 10 — Module Bulletins

### `modules/bulletins/generer.php`

```php
<?php
// =============================================================
// modules/bulletins/generer.php
// Génère le bulletin de notes d'un étudiant.
// Le bulletin affiche :
//   - les notes par matière (avec moyenne par matière)
//   - la moyenne générale (pondérée par les coefficients)
//   - la mention obtenue
//   - le rang dans la classe
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$pageTitle    = 'Bulletin de notes';
$etudiantId   = (int)($_GET['etudiant_id'] ?? 0);
$classeId     = (int)($_GET['classe_id']   ?? 0);

// Listes pour les menus déroulants
$classes   = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();
$etudiants = [];

if ($classeId > 0) {
    $stmt = $pdo->prepare("
        SELECT id, nom, prenom, matricule
        FROM etudiants
        WHERE classe_id = :classe_id
        ORDER BY nom, prenom
    ");
    $stmt->execute([':classe_id' => $classeId]);
    $etudiants = $stmt->fetchAll();
}

// ---------------------------------------------------------------
// Génération du bulletin si un étudiant est sélectionné
// ---------------------------------------------------------------
$bulletin       = null;
$classement     = null;

if ($etudiantId > 0) {

    // Informations complètes de l'étudiant
    $stmt = $pdo->prepare("
        SELECT e.*, c.nom AS classe_nom, c.niveau, c.annee_scolaire
        FROM etudiants e
        LEFT JOIN classes c ON e.classe_id = c.id
        WHERE e.id = :id
    ");
    $stmt->execute([':id' => $etudiantId]);
    $etudiantInfo = $stmt->fetch();

    if ($etudiantInfo) {
        // Récupération des matières de la classe de l'étudiant
        $stmt = $pdo->prepare("
            SELECT m.id, m.nom, m.code, m.coefficient,
                   CONCAT(ens.prenom, ' ', ens.nom) AS enseignant
            FROM matieres m
            LEFT JOIN enseignants ens ON m.enseignant_id = ens.id
            WHERE m.classe_id = :classe_id
            ORDER BY m.nom
        ");
        $stmt->execute([':classe_id' => $etudiantInfo['classe_id']]);
        $matieres = $stmt->fetchAll();

        $lignesBulletin    = [];
        $totalPoints       = 0; // pour la moyenne générale
        $totalCoefficients = 0;

        foreach ($matieres as $matiere) {
            // Récupération de toutes les notes de cet étudiant dans cette matière
            $stmtNotes = $pdo->prepare("
                SELECT type_evaluation, note, date_evaluation
                FROM notes
                WHERE etudiant_id = :etudiant_id AND matiere_id = :matiere_id
                ORDER BY date_evaluation
            ");
            $stmtNotes->execute([
                ':etudiant_id' => $etudiantId,
                ':matiere_id'  => $matiere['id'],
            ]);
            $notesMatiere = $stmtNotes->fetchAll();

            // Calcul de la moyenne par matière (simple : somme / nombre)
            $moyenneMatiere = 0;
            if (!empty($notesMatiere)) {
                $somme = array_sum(array_column($notesMatiere, 'note'));
                $moyenneMatiere = round($somme / count($notesMatiere), 2);
            }

            // Accumulation pour la moyenne générale pondérée
            $totalPoints       += $moyenneMatiere * $matiere['coefficient'];
            $totalCoefficients += $matiere['coefficient'];

            $lignesBulletin[] = [
                'matiere'    => $matiere['nom'],
                'code'       => $matiere['code'],
                'coeff'      => $matiere['coefficient'],
                'enseignant' => $matiere['enseignant'],
                'notes'      => $notesMatiere,
                'moyenne'    => $moyenneMatiere,
                'mention'    => getMention($moyenneMatiere),
                'couleur'    => getCouleurMention($moyenneMatiere),
            ];
        }

        // Calcul de la moyenne générale pondérée
        $moyenneGenerale = $totalCoefficients > 0
            ? round($totalPoints / $totalCoefficients, 2)
            : 0;

        $bulletin = [
            'etudiant'  => $etudiantInfo,
            'lignes'    => $lignesBulletin,
            'moyenne'   => $moyenneGenerale,
            'mention'   => getMention($moyenneGenerale),
            'couleur'   => getCouleurMention($moyenneGenerale),
        ];

        // -------------------------------------------------------
        // Calcul du classement de l'étudiant dans sa classe
        // -------------------------------------------------------
        $stmtClassement = $pdo->prepare("
            SELECT e.id, e.nom, e.prenom,
                   SUM(n.note * m.coefficient) / SUM(m.coefficient) AS moyenne_generale
            FROM etudiants e
            JOIN notes n    ON n.etudiant_id = e.id
            JOIN matieres m ON m.id = n.matiere_id
            WHERE e.classe_id = :classe_id
            GROUP BY e.id, e.nom, e.prenom
            ORDER BY moyenne_generale DESC
        ");
        $stmtClassement->execute([':classe_id' => $etudiantInfo['classe_id']]);
        $classementClasse = $stmtClassement->fetchAll();

        // Trouver le rang de l'étudiant
        $rang = 1;
        foreach ($classementClasse as $index => $entry) {
            if ($entry['id'] == $etudiantId) {
                $rang = $index + 1;
                break;
            }
        }

        $bulletin['rang']    = $rang;
        $bulletin['effectif'] = count($classementClasse);
    }
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-file-earmark-text text-primary me-2"></i>Génération de bulletin</h2>
    <?php if ($bulletin): ?>
    <button onclick="window.print()" class="btn btn-outline-secondary">
        <i class="bi bi-printer me-1"></i>Imprimer
    </button>
    <?php endif; ?>
</div>

<!-- Sélection de l'étudiant -->
<div class="card shadow-sm mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Classe</label>
                <select class="form-select" name="classe_id" onchange="this.form.submit()">
                    <option value="0">— Choisir une classe —</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classeId == $c['id'] ? 'selected' : '' ?>>
                        <?= clean($c['nom']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!empty($etudiants)): ?>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Étudiant</label>
                <select class="form-select" name="etudiant_id">
                    <option value="0">— Choisir un étudiant —</option>
                    <?php foreach ($etudiants as $e): ?>
                    <option value="<?= $e['id'] ?>" <?= $etudiantId == $e['id'] ? 'selected' : '' ?>>
                        <?= clean($e['nom'] . ' ' . $e['prenom'] . ' (' . $e['matricule'] . ')') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-file-earmark me-1"></i>Générer le bulletin
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bulletin généré -->
<?php if ($bulletin): ?>
<?php $e = $bulletin['etudiant']; ?>

<div class="card shadow" id="bulletin-print">
    <!-- En-tête du bulletin -->
    <div class="bulletin-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="fw-bold mb-1">
                    <i class="bi bi-mortarboard-fill me-2"></i>Institut Supérieur d'Informatique
                </h3>
                <p class="mb-0 opacity-75">Bulletin de Notes — Semestre en cours</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="fs-5">Année <?= clean($e['annee_scolaire'] ?? '') ?></div>
                <div class="opacity-75">Classe : <?= clean($e['classe_nom'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <div class="card-body">
        <!-- Informations de l'étudiant -->
        <div class="row mb-4 p-3 bg-light rounded">
            <div class="col-md-3">
                <small class="text-muted">Matricule</small>
                <div class="fw-bold"><code><?= clean($e['matricule']) ?></code></div>
            </div>
            <div class="col-md-4">
                <small class="text-muted">Nom et prénom</small>
                <div class="fw-bold fs-5"><?= clean($e['nom'] . ' ' . $e['prenom']) ?></div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Date de naissance</small>
                <div><?= formatDate($e['date_naissance']) ?></div>
            </div>
            <div class="col-md-2 text-center">
                <small class="text-muted">Rang</small>
                <div class="fw-bold fs-4 text-primary">
                    <?= $bulletin['rang'] ?><sup>e</sup> / <?= $bulletin['effectif'] ?>
                </div>
            </div>
        </div>

        <!-- Tableau des notes -->
        <div class="table-responsive">
            <table class="table table-bordered bulletin-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Matière</th>
                        <th>Enseignant</th>
                        <th>Coef.</th>
                        <th>Notes obtenues</th>
                        <th>Moyenne / 20</th>
                        <th>Mention</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bulletin['lignes'] as $ligne): ?>
                    <tr>
                        <td><code><?= clean($ligne['code']) ?></code></td>
                        <td class="fw-semibold"><?= clean($ligne['matiere']) ?></td>
                        <td class="text-muted small"><?= clean($ligne['enseignant'] ?? '—') ?></td>
                        <td class="text-center"><?= $ligne['coeff'] ?></td>
                        <td>
                            <?php foreach ($ligne['notes'] as $n): ?>
                            <span class="badge bg-secondary me-1">
                                <?= clean($n['type_evaluation']) ?> : <?= number_format($n['note'], 2) ?>
                            </span>
                            <?php endforeach; ?>
                            <?php if (empty($ligne['notes'])): ?>
                            <span class="text-muted fst-italic">Pas de note</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-bold fs-5">
                            <span class="text-<?= $ligne['couleur'] ?>">
                                <?= number_format($ligne['moyenne'], 2) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?= $ligne['couleur'] ?> badge-mention">
                                <?= $ligne['mention'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="5" class="text-end fw-bold">MOYENNE GÉNÉRALE</td>
                        <td class="text-center fw-bold fs-4">
                            <?= number_format($bulletin['moyenne'], 2) ?> / 20
                        </td>
                        <td>
                            <span class="badge bg-<?= $bulletin['couleur'] ?> fs-6 badge-mention">
                                <?= $bulletin['mention'] ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Décision du jury -->
        <div class="p-3 mt-3 rounded border <?= $bulletin['moyenne'] >= 10 ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' ?>">
            <strong>Décision :</strong>
            <?php if ($bulletin['moyenne'] >= 10): ?>
            <span class="text-success fw-bold">✅ ADMIS(E)</span>
            — L'étudiant(e) a satisfait aux exigences du semestre.
            <?php else: ?>
            <span class="text-danger fw-bold">❌ NON ADMIS(E)</span>
            — La moyenne générale est inférieure à 10/20.
            <?php endif; ?>
        </div>

        <div class="row mt-4 pt-3 border-top">
            <div class="col-6 text-center">
                <p class="text-muted small">Signature de l'étudiant(e)</p>
                <div style="height: 60px; border-bottom: 1px solid #ccc; width: 80%; margin: 0 auto;"></div>
            </div>
            <div class="col-6 text-center">
                <p class="text-muted small">Cachet et signature de la Direction</p>
                <div style="height: 60px; border-bottom: 1px solid #ccc; width: 80%; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
@media print {
    .no-print, nav, footer { display: none !important; }
    .bulletin-header { -webkit-print-color-adjust: exact; }
}
</style>

<?php require_once '../../includes/footer.php'; ?>
```

---

> ### ✅ Point de vérification — Étape 10
> 1. Accédez au module Bulletins.
> 2. Sélectionnez la classe `L2-Info-A`.
> 3. Sélectionnez l'étudiant **Ibrahima Sarr** et générez son bulletin.
> 4. Vérifiez que :
>    - Les moyennes par matière sont correctement calculées.
>    - La moyenne générale tient compte des coefficients.
>    - La mention est correcte (Ibrahima devrait avoir Très Bien ou Bien selon ses notes).
>    - Le rang s'affiche correctement.
> 5. Testez le bouton "Imprimer" → le bulletin doit s'afficher proprement sans la navigation.
>
> ➡️ Si le bulletin est correct et imprimable, votre application est fonctionnelle ! 🎉

---

## Étape 11 — Module Classement

### `modules/bulletins/classement.php`

```php
<?php
// =============================================================
// modules/bulletins/classement.php
// Affiche le classement des étudiants d'une classe par moyenne.
// Les 3 premiers reçoivent une distinction.
// =============================================================

require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireLogin();

$pageTitle = 'Classement des étudiants';
$classeId  = (int)($_GET['classe_id'] ?? 0);
$classes   = $pdo->query("SELECT id, nom FROM classes ORDER BY nom")->fetchAll();

$classement = [];

if ($classeId > 0) {
    // Calcul des moyennes générales pondérées pour chaque étudiant de la classe
    $stmt = $pdo->prepare("
        SELECT
            e.id,
            e.matricule,
            e.nom,
            e.prenom,
            e.sexe,
            ROUND(SUM(n.note * m.coefficient) / SUM(m.coefficient), 2) AS moyenne_generale
        FROM etudiants e
        JOIN notes n    ON n.etudiant_id = e.id
        JOIN matieres m ON m.id = n.matiere_id
        WHERE e.classe_id = :classe_id
        GROUP BY e.id, e.matricule, e.nom, e.prenom, e.sexe
        ORDER BY moyenne_generale DESC
    ");
    $stmt->execute([':classe_id' => $classeId]);
    $classement = $stmt->fetchAll();
}

require_once '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold"><i class="bi bi-trophy-fill text-warning me-2"></i>Classement des étudiants</h2>
</div>

<!-- Sélection de la classe -->
<form method="GET" class="card shadow-sm mb-4">
    <div class="card-body d-flex gap-3 align-items-end">
        <div class="flex-grow-1">
            <label class="form-label fw-semibold">Sélectionner une classe</label>
            <select class="form-select" name="classe_id">
                <option value="0">— Choisir —</option>
                <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $classeId == $c['id'] ? 'selected' : '' ?>>
                    <?= clean($c['nom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-bar-chart me-1"></i>Afficher le classement
        </button>
    </div>
</form>

<!-- Tableau de classement -->
<?php if (!empty($classement)): ?>
<div class="card shadow">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">
            <i class="bi bi-list-ol me-2"></i>
            Classement — <?= count($classement) ?> étudiants classés
        </h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th class="text-center">Rang</th>
                    <th>Matricule</th>
                    <th>Nom complet</th>
                    <th class="text-center">Moyenne / 20</th>
                    <th class="text-center">Mention</th>
                    <th class="text-center">Distinction</th>
                    <th>Bulletin</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($classement as $rang => $e): ?>
                <?php
                    $rang1base = $rang + 1;
                    $mention   = getMention((float)$e['moyenne_generale']);
                    $couleur   = getCouleurMention((float)$e['moyenne_generale']);

                    // Distinctions pour les 3 premiers
                    $distinction = '';
                    if ($rang1base === 1) $distinction = '🥇 Premier de classe';
                    elseif ($rang1base === 2) $distinction = '🥈 Deuxième de classe';
                    elseif ($rang1base === 3) $distinction = '🥉 Troisième de classe';
                ?>
                <tr class="<?= $rang1base <= 3 ? 'table-warning fw-semibold' : '' ?>">
                    <td class="text-center">
                        <span class="badge bg-<?= $rang1base <= 3 ? 'warning text-dark' : 'secondary' ?> fs-6">
                            <?= $rang1base ?>
                        </span>
                    </td>
                    <td><code><?= clean($e['matricule']) ?></code></td>
                    <td><?= clean($e['nom'] . ' ' . $e['prenom']) ?></td>
                    <td class="text-center fs-5 fw-bold text-<?= $couleur ?>">
                        <?= number_format((float)$e['moyenne_generale'], 2) ?>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-<?= $couleur ?>"><?= $mention ?></span>
                    </td>
                    <td class="text-center"><?= $distinction ?></td>
                    <td>
                        <a href="generer.php?classe_id=<?= $classeId ?>&etudiant_id=<?= $e['id'] ?>"
                           class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-file-text"></i> Voir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($classeId > 0): ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Aucune note n'a encore été saisie pour cette classe.
</div>
<?php endif; ?>

<?php require_once '../../includes/footer.php'; ?>
```

---

### Ajout du lien vers le classement dans le header

Dans `includes/header.php`, ajoutez ce lien dans le menu "Académique" :

```php
<li><a class="dropdown-item" href="/gestion_scolaire/modules/bulletins/classement.php">Classement</a></li>
```

---

> ### ✅ Point de vérification — Étape 11 (Vérification finale)
> Effectuez un test complet de l'application :
>
> | # | Test | Résultat attendu |
> |---|---|---|
> | 1 | Connexion avec admin@isi.sn | Redirection vers le dashboard |
> | 2 | Dashboard | Statistiques correctes (5 étudiants, 5 matières…) |
> | 3 | Ajouter un 6ème étudiant | Succès + message de confirmation |
> | 4 | Modifier cet étudiant | Modifications enregistrées |
> | 5 | Supprimer cet étudiant | Suppression confirmée |
> | 6 | Saisir des notes pour "Anglais Technique" | Notes en BDD |
> | 7 | Générer le bulletin d'Ibrahima Sarr | Bulletin complet avec rang |
> | 8 | Afficher le classement de L2-Info-A | Classement trié par moyenne |
> | 9 | Accéder à une URL sans être connecté | Redirection vers login |
> | 10 | Se déconnecter | Retour à la page de connexion |
>
> ➡️ **Si tous les tests passent : félicitations, votre application est fonctionnelle ! 🎓**

---

## Points d'amélioration

Voici **10 pistes d'amélioration** pour enrichir cette application après avoir terminé l'atelier :

### 1. 📧 Envoi de bulletins par email
Intégrer la bibliothèque **PHPMailer** pour envoyer automatiquement le bulletin en PDF à chaque étudiant (via `mail()` ou SMTP).

### 2. 📄 Export PDF du bulletin
Utiliser la bibliothèque **TCPDF** ou **mPDF** pour générer un vrai fichier PDF du bulletin, téléchargeable et envoyable, au lieu du simple affichage HTML.

### 3. 🔐 Gestion fine des permissions
Implémenter un système de droits plus granulaire : un enseignant ne peut saisir des notes **que pour ses propres matières**, et un étudiant ne voit **que son propre bulletin**.

### 4. 📊 Graphiques de performances
Ajouter des graphiques (via **Chart.js**) dans le tableau de bord pour visualiser l'évolution des moyennes dans le temps, la répartition des mentions par classe, etc.

### 5. 🗂️ Gestion multi-semestres
Permettre de gérer plusieurs semestres par année scolaire, avec archivage des résultats et calcul d'une moyenne annuelle.

### 6. 🔍 Historique et journalisation (logs)
Enregistrer dans une table `logs` toutes les actions importantes (qui a modifié quoi, à quelle date), pour assurer la traçabilité des modifications.

### 7. 🧑‍💻 Espace étudiant avec authentification propre
Créer un portail dédié aux étudiants où ils peuvent se connecter avec leur matricule, consulter leurs notes en temps réel et télécharger leurs bulletins.

### 8. 📱 Application responsive améliorée
Optimiser l'interface pour les smartphones (affichage des tableaux sur petits écrans, navigation mobile, PWA progressive web app).

### 9. 🔁 Import/Export CSV
Permettre à l'administrateur d'importer une liste d'étudiants via un fichier CSV (avec `fgetcsv()`) et d'exporter les résultats en Excel ou CSV pour traitement externe.

### 10. 🧮 Gestion des absences et calcul de l'assiduité
Ajouter un module de présences/absences lié aux évaluations, avec un seuil d'avertissement automatique si un étudiant dépasse un certain nombre d'absences, et la prise en compte de l'assiduité dans le conseil de classe.

---

## Récapitulatif des fichiers créés

| Fichier | Rôle |
|---|---|
| `config/database.php` | Connexion PDO à MySQL |
| `includes/functions.php` | Fonctions utilitaires (clean, getMention…) |
| `includes/auth.php` | Authentification et sessions |
| `includes/header.php` | En-tête HTML + barre de navigation |
| `includes/footer.php` | Pied de page HTML |
| `assets/css/style.css` | Styles personnalisés |
| `login.php` | Page de connexion |
| `logout.php` | Déconnexion |
| `dashboard.php` | Tableau de bord avec statistiques |
| `modules/etudiants/liste.php` | Liste + recherche des étudiants |
| `modules/etudiants/ajouter.php` | Formulaire d'ajout |
| `modules/etudiants/modifier.php` | Formulaire de modification |
| `modules/etudiants/supprimer.php` | Suppression sécurisée |
| `modules/notes/saisie.php` | Saisie des notes par matière |
| `modules/bulletins/generer.php` | Génération du bulletin complet |
| `modules/bulletins/classement.php` | Classement par moyenne |

---

*Atelier réalisé dans le cadre du cours de Développement Web PHP — Institut Supérieur d'Informatique (ISI) — Licence 2*