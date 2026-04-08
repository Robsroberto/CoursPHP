### **Travaux Dirigés : Connexion, Insertion et Récupération des Données avec PHP et MySQL**

Ce TP vous guidera dans l'apprentissage de la gestion des bases de données MySQL avec PHP. Vous travaillerez avec deux interfaces : **MySQLi** et **PDO**.

---

### **Objectifs :**
1. Apprendre à connecter une application PHP à une base de données MySQL.
2. Maîtriser l'insertion de données dans une base de données.
3. Savoir récupérer et afficher les données depuis une base de données.
4. Comparer l'utilisation de **MySQLi procédural** et **PDO**.

---

### **Partie 1 : Préparation de la Base de Données**

#### 1.1. Création de la base de données et de la table
Avant de commencer avec PHP, vous devez créer une base de données et une table MySQL.

1. Connectez-vous à **phpMyAdmin** ou utilisez un client MySQL.
2. Créer une base de donné visuellement nommée tpPHP avec phpmyadmin ou Exécutez le script SQL suivant pour créer une base de données `tpPHP` et une table `etudiants` :

```sql
CREATE DATABASE tp_php;

USE tp_php;

CREATE TABLE etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **Partie 2 : Connexion à la base de données**

#### **Étape 1 : Connexion avec MySQLi en mode procédural**
1. Créez un fichier `mysqli_connect.php` pour essayer de vous connecter a votre base de donnée avec un message d'erreur en cas d'echec ou de succés en cas de reussite

<!-- ```php
<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "tp_php";

// Connexion à la base de données
$conn = mysqli_connect($host, $user, $password, $dbname);

// Vérification de la connexion
if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

echo "Connexion réussie avec MySQLi !";
?>
``` -->

#### **Étape 2 : Connexion avec PDO**
1. Créez un fichier `pdo_connect.php` pour le même type de connexion que celui du `mysqli`

<!-- ```php
<?php
$host = "localhost";
$dbname = "tp_php";
$user = "root";
$password = "";

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connexion réussie avec PDO !";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
``` -->

---

### **Partie 3 : Insertion de données dans la base**

#### **Étape 1 : Insertion avec MySQLi (procédural)**
1. Créez un fichier `mysqli_insert.php` pour insérer un étudiant dans la table `etudiants` suivant leur nom, prenom, email grace aux données a une requete Sql.

<!-- ```php
<?php
require 'mysqli_connect.php'; // Inclure le fichier de connexion

// Données à insérer
$nom = "Dupont";
$prenom = "Jean";
$email = "jean.dupont@example.com";

// Requête d'insertion
$sql = "INSERT INTO etudiants (nom, prenom, email) VALUES ('$nom', '$prenom', '$email')";

if (mysqli_query($conn, $sql)) {
    echo "Nouvel étudiant ajouté avec succès !";
} else {
    echo "Erreur : " . mysqli_error($conn);
}

// Fermer la connexion
mysqli_close($conn);
?>
``` -->

#### **Étape 2 : Insertion avec PDO**
1. Créez un fichier `pdo_insert.php` pour insérer un étudiant dans la table `etudiants`suivant leur nom, prenom, email grace aux données a une requete Sql.

<!-- ```php
<?php
require 'pdo_connect.php'; // Inclure le fichier de connexion

// Données à insérer
$nom = "Durand";
$prenom = "Marie";
$email = "marie.durand@example.com";

try {
    // Requête d'insertion
    $sql = "INSERT INTO etudiants (nom, prenom, email) VALUES (:nom, :prenom, :email)";
    $stmt = $pdo->prepare($sql);

    // Liaison des paramètres
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);

    // Exécution de la requête
    $stmt->execute();
    echo "Nouvel étudiant ajouté avec succès !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
``` -->

---

### **Partie 4 : Récupération des données depuis la base**

#### **Étape 1 : Récupération avec MySQLi (procédural)**
1. Créez un fichier `mysqli_select.php` pour afficher tous les étudiants suivant les colonnes de la base et grace a une requete sql.

<!-- ```php
<?php
require 'mysqli_connect.php'; // Inclure le fichier de connexion

// Requête de sélection
$sql = "SELECT * FROM etudiants";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Affichage des résultats
    while ($row = mysqli_fetch_assoc($result)) {
        echo "ID : " . $row['id'] . " - Nom : " . $row['nom'] . " " . $row['prenom'] . " - Email : " . $row['email'] . "<br>";
    }
} else {
    echo "Aucun étudiant trouvé.";
}

// Fermer la connexion
mysqli_close($conn);
?>
``` -->

#### **Étape 2 : Récupération avec PDO**
1. Créez un fichier `pdo_select.php` pour afficher tous les étudiants suivant les colonnes de la base et grace a une requete sql.

<!-- ```php
<?php
require 'pdo_connect.php'; // Inclure le fichier de connexion

try {
    // Requête de sélection
    $sql = "SELECT * FROM etudiants";
    $stmt = $pdo->query($sql);

    // Affichage des résultats
    foreach ($stmt as $row) {
        echo "ID : " . $row['id'] . " - Nom : " . $row['nom'] . " " . $row['prenom'] . " - Email : " . $row['email'] . "<br>";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
``` -->

---

### **Partie 5 : Comparaison entre MySQLi et PDO**

**Points à discuter :**
1. **Flexibilité :** PDO fonctionne avec plusieurs bases de données (MySQL, PostgreSQL, SQLite, etc.), contrairement à MySQLi qui est spécifique à MySQL.
2. **Sécurité :** Les deux permettent de prévenir les injections SQL grâce aux requêtes préparées, mais PDO offre une gestion plus élégante des paramètres.
3. **Simplicité :** MySQLi est souvent plus simple pour les débutants grâce à sa syntaxe directe.

---

### **Exercices pratiques**

#### **Exercice 1 : Connexion et insertion**
- Créez une page PHP qui se connecte à la base de données, insère un étudiant en récupérant les données depuis un formulaire HTML, puis affiche un message de confirmation.

#### **Exercice 2 : Affichage dynamique**
- Modifiez le script de récupération pour afficher les données des étudiants dans un tableau HTML.

#### **Exercice 3 : Mise à jour et suppression**
- Ajoutez des scripts pour mettre à jour les données d’un étudiant (modifier son email) et supprimer un étudiant (via son `id`).

---

### **Structure des fichiers**
1. `mysqli_connect.php` et `pdo_connect.php` (connexion).
2. `mysqli_insert.php` et `pdo_insert.php` (insertion).
3. `mysqli_select.php` et `pdo_select.php` (récupération).
4. Scripts pour formulaire et actions complémentaires.

---

### **Conclusion**
Ce TP permet de maîtriser les bases de l’interaction entre PHP et MySQL, avec un accent sur la sécurité grâce aux requêtes préparées.