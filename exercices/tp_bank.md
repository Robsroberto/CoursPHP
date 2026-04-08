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
paginate: 
footer: Licence 2 Informatique &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ISI (Institut Supérieur D'Informatique) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; RD
---
<img src="../isi.png" alt="ISI" width="100px">
  



### TP : Création d'un Système Bancaire en PHP![miniature](php.png)

## Par Robert DIASSÉ
### Note Importante


La documentation officielle de PHP est disponible à l'adresse suivante : [https://www.php.net/docs.php](https://www.php.net/docs.php)

En vous appuyant sur cette ressource essentielle, vous pourrez :

- Comprendre en détail le fonctionnement des fonctions et des structures de PHP.
- Accéder à des exemples pratiques pour illustrer l'utilisation correcte des différentes fonctionnalités.
- Découvrir les dernières fonctionnalités ajoutées aux nouvelles versions de PHP.
- Explorer les recommandations de sécurité pour écrire un code robuste et sécurisé.
- Résoudre des problèmes et des erreurs courantes en trouvant des solutions éprouvées.

L'apprentissage et l'utilisation continue de la documentation officielle vous permettront de développer vos compétences de manière autonome et de rester à jour avec les meilleures pratiques et les avancées du langage PHP.
#### Schéma Relationnel

1. **Table `clients`** (<u>`id_c`</u>, `nom`, `prenom`, `email`, `mot_de_passe`, `date_creation`)

    - `id` : Identifiant unique (clé primaire).
    - `nom` : Nom du client.
    - `prenom` : Prénom du client.
    - `email` : Adresse email du client (unique).
    - `mot_de_passe` : Mot de passe du client (haché).
    - `date_creation` : Date de création du compte client.

2. **Table `comptes`** (<u>`id_comp`</u>, #`client_id`, `numero_compte`, `solde`, `date_creation`)

    - `id` : Identifiant unique (clé primaire).
    - `client_id` : Référence à l'identifiant du client (clé étrangère).
    - `numero_compte` : Numéro unique du compte.
    - `solde` : Solde du compte.
    - `date_creation` : Date de création du compte.

3. **Table `transactions`** (<u>`id_t`</u>, #`compte_id`, `type_transaction`, `montant`, `date_transaction`)

    - `id` : Identifiant unique (clé primaire).
    - `compte_id` : Référence à l'identifiant du compte (clé étrangère).
    - `type_transaction` : Type de transaction (`depot` ou `retrait`).
    - `montant` : Montant de la transaction.
    - `date_transaction` : Date de la transaction.

#### Fonctionnalités et Étapes

1. **Créer un Client et un Compte Bancaire**

   **Étapes** :
   - Créer le formulaire pour ajouter un client.
   - Créer le script PHP pour insérer les informations du client dans la base de données.
   - Associer un compte bancaire au client nouvellement créé.

   **Code Exemple** :

   **Formulaire d'inscription du client** :
   ```html
   <!-- form_client.html -->
   <form action="creer_client.php" method="POST">
       <label for="nom">Nom :</label>
       <input type="text" id="nom" name="nom" required>
       
       <label for="prenom">Prénom :</label>
       <input type="text" id="prenom" name="prenom" required>
       
       <label for="email">Email :</label>
       <input type="email" id="email" name="email" required>
       
       <label for="mot_de_passe">Mot de passe :</label>
       <input type="password" id="mot_de_passe" name="mot_de_passe" required>
       
       <button type="submit">Créer le compte</button>
   </form>
   ```

   **Script PHP pour créer un client et un compte** :
   ```php
   // creer_client.php
   <?php
   // Connexion à la base de données
   $pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

   function creerClient($nom, $prenom, $email, $mot_de_passe) {
       global $pdo;
       $stmt = $pdo->prepare("INSERT INTO clients (nom, prenom, email, mot_de_passe, date_creation) VALUES (?, ?, ?, ?, NOW())");
       $stmt->execute([$nom, $prenom, $email, password_hash($mot_de_passe, PASSWORD_BCRYPT)]);
       return $pdo->lastInsertId();
   }

   function creerCompte($client_id, $numero_compte) {
       global $pdo;
       $stmt = $pdo->prepare("INSERT INTO comptes (client_id, numero_compte, solde, date_creation) VALUES (?, ?, 0, NOW())");
       $stmt->execute([$client_id, $numero_compte]);
   }

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $nom = $_POST['nom'];
       $prenom = $_POST['prenom'];
       $email = $_POST['email'];
       $mot_de_passe = $_POST['mot_de_passe'];

       try {
           $client_id = creerClient($nom, $prenom, $email, $mot_de_passe);
           creerCompte($client_id, 'FR7612345678901234567890123');
           echo "Client et compte créés avec succès.";
       } catch (Exception $e) {
           echo "Erreur : " . $e->getMessage();
       }
   }
   ?>
   ```

2. **Effectuer des Dépôts et Retraits**

   **Étapes** :
   - Créer le formulaire pour effectuer des dépôts et des retraits.
   - Créer les scripts PHP pour mettre à jour le solde du compte et enregistrer les transactions.

   **Formulaire pour les transactions** :
   ```html
   <!-- form_transaction.html -->
   <form action="transaction.php" method="POST">
       <label for="compte_id">ID du Compte :</label>
       <input type="text" id="compte_id" name="compte_id" required>
       
       <label for="type_transaction">Type de Transaction :</label>
       <select id="type_transaction" name="type_transaction" required>
           <option value="depot">Dépot</option>
           <option value="retrait">Retrait</option>
       </select>
       
       <label for="montant">Montant :</label>
       <input type="number" id="montant" name="montant" required>
       
       <button type="submit">Effectuer la transaction</button>
   </form>
   ```

   **Script PHP pour les transactions** :
   ```php
   // transaction.php
   <?php
   // Connexion à la base de données
   $pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

   function deposer($compte_id, $montant) {
       global $pdo;
       $pdo->beginTransaction();
       try {
           $stmt = $pdo->prepare("UPDATE comptes SET solde = solde + ? WHERE id = ?");
           $stmt->execute([$montant, $compte_id]);

           $stmt = $pdo->prepare("INSERT INTO transactions (compte_id, type_transaction, montant, date_transaction) VALUES (?, 'depot', ?, NOW())");
           $stmt->execute([$compte_id, $montant]);

           $pdo->commit();
       } catch (Exception $e) {
           $pdo->rollBack();
           throw $e;
       }
   }

   function retirer($compte_id, $montant) {
       global $pdo;
       $pdo->beginTransaction();
       try {
           $stmt = $pdo->prepare("SELECT solde FROM comptes WHERE id = ?");
           $stmt->execute([$compte_id]);
           $solde = $stmt->fetchColumn();

           if ($solde < $montant) {
               throw new Exception('Solde insuffisant');
           }

           $stmt = $pdo->prepare("UPDATE comptes SET solde = solde - ? WHERE id = ?");
           $stmt->execute([$montant, $compte_id]);

           $stmt = $pdo->prepare("INSERT INTO transactions (compte_id, type_transaction, montant, date_transaction) VALUES (?, 'retrait', ?, NOW())");
           $stmt->execute([$compte_id, $montant]);

           $pdo->commit();
       } catch (Exception $e) {
           $pdo->rollBack();
           throw $e;
       }
   }

   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
       $compte_id = $_POST['compte_id'];
       $type_transaction = $_POST['type_transaction'];
       $montant = $_POST['montant'];

       try {
           if ($type_transaction == 'depot') {
               deposer($compte_id, $montant);
           } elseif ($type_transaction == 'retrait') {
               retirer($compte_id, $montant);
           }
           echo "Transaction effectuée avec succès.";
       } catch (Exception $e) {
           echo "Erreur : " . $e->getMessage();
       }
   }
   ?>
   ```

3. **Afficher l'Historique des Transactions**

   **Étapes** :
   - Créer une page pour afficher l'historique des transactions d'un compte spécifique.
   - Récupérer les transactions depuis la base de données et les afficher.

   **Formulaire pour sélectionner un compte** :
   ```html
   <!-- form_historique.html -->
   <form action="historique.php" method="GET">
       <label for="compte_id">ID du Compte :</label>
       <input type="text" id="compte_id" name="compte_id" required>
       <button type="submit">Afficher l'historique</button>
   </form>
   ```

   **Script PHP pour afficher l'historique des transactions** :
   ```php
   // historique.php
   <?php
   // Connexion à la base de données
   $pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

   function getTransactions($compte_id) {
       global $pdo;
       $stmt = $pdo->prepare("SELECT * FROM transactions WHERE compte_id = ? ORDER BY date_transaction DESC");
       $stmt->execute([$compte_id]);
       return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   if (isset($_GET['compte_id'])) {
       $compte_id = $_GET['compte_id'];
       $transactions = getTransactions($compte_id);
       echo "<h2>Historique des Transactions pour le Compte ID: $compte_id</h2>";
       echo "<table border='1'>
               <tr>
                   <th>ID</th>
                   <th>Type</th>
                   <th>Montant</th>
                   <th>Date</th>
               </tr>";
       foreach ($transactions as $transaction) {
           echo "<tr>
                   <td>" . $transaction['id'] . "</td>
                   <td>" . $transaction['type_transaction'] . "</td>
                   <td>" . $transaction['montant'] . "</td>
                   <td>" . $transaction['date_transaction'] . "</td>
                 </tr>";
       }
       echo "</table>";
   }
   ?>
   ```
### Étapes d'Amélioration du Projet Bancaire en PHP

Ce TP vous a permis de créer une application bancaire simple en PHP, comprenant la création de clients et de comptes, la gestion des transactions (dépôts et retraits), et l'affichage de l'historique des transactions. Pour rendre cette application plus robuste et fonctionnelle, voici une liste d'améliorations que vous pouvez envisager :

#### 1. Affichage de la Liste des Clients

**Étapes :**
- Créer une page pour afficher tous les clients.
- Récupérer les informations des clients depuis la base de données et les afficher dans un tableau.

<!-- **Exemple de code :** -->
<!-- ```php
// liste_clients.php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

function getClients() {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM clients ORDER BY nom, prenom");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$clients = getClients();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Liste des Clients</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date de Création</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['id']) ?></td>
                        <td><?= htmlspecialchars($client['nom']) ?></td>
                        <td><?= htmlspecialchars($client['prenom']) ?></td>
                        <td><?= htmlspecialchars($client['email']) ?></td>
                        <td><?= htmlspecialchars($client['date_creation']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
``` -->

#### 2. Affichage des Transactions d'un Client

**Étapes :**
- Créer une page pour afficher toutes les transactions d'un client spécifique.
- Récupérer les transactions depuis la base de données et les afficher dans un tableau.

<!-- **Exemple de code :**
```php
// transactions_client.php
<?php
$pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

function getTransactionsByClient($client_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT t.*, c.numero_compte FROM transactions t JOIN comptes c ON t.compte_id = c.id WHERE c.client_id = ? ORDER BY t.date_transaction DESC");
    $stmt->execute([$client_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];
    $transactions = getTransactionsByClient($client_id);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transactions du Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Transactions du Client ID: <?= htmlspecialchars($client_id) ?></h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Compte</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($transactions)): ?>
                    <?php foreach ($transactions as $transaction): ?>
                        <tr>
                            <td><?= htmlspecialchars($transaction['id']) ?></td>
                            <td><?= htmlspecialchars($transaction['numero_compte']) ?></td>
                            <td><?= htmlspecialchars($transaction['type_transaction']) ?></td>
                            <td><?= htmlspecialchars($transaction['montant']) ?></td>
                            <td><?= htmlspecialchars($transaction['date_transaction']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
``` -->

#### 3. Recherche par Date ou Numéro de Compte

**Étapes :**
- Ajouter un formulaire de recherche par date et par numéro de compte.
- Créer un script pour traiter la recherche et afficher les résultats.

<!-- **Exemple de code :**
```php
// recherche_transactions.php
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche de Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Recherche de Transactions</h2>
        <form action="recherche_transactions.php" method="GET">
            <div class="mb-3">
                <label for="date_debut" class="form-label">Date de début :</label>
                <input type="date" id="date_debut" name="date_debut" class="form-control">
            </div>
            <div class="mb-3">
                <label for="date_fin" class="form-label">Date de fin :</label>
                <input type="date" id="date_fin" name="date_fin" class="form-control">
            </div>
            <div class="mb-3">
                <label for="numero_compte" class="form-label">Numéro de Compte :</label>
                <input type="text" id="numero_compte" name="numero_compte" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
        <br>
        <?php
        $pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

        if (isset($_GET['date_debut']) && isset($_GET['date_fin']) && isset($_GET['numero_compte'])) {
            $date_debut = $_GET['date_debut'];
            $date_fin = $_GET['date_fin'];
            $numero_compte = $_GET['numero_compte'];

            $stmt = $pdo->prepare("SELECT t.*, c.numero_compte FROM transactions t JOIN comptes c ON t.compte_id = c.id WHERE c.numero_compte = ? AND t.date_transaction BETWEEN ? AND ? ORDER BY t.date_transaction DESC");
            $stmt->execute([$numero_compte, $date_debut, $date_fin]);
            $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo "<h3>Résultats de la recherche :</h3>";
            echo "<table class='table table-striped'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Compte</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>";
            foreach ($transactions as $transaction) {
                echo "<tr>
                        <td>" . htmlspecialchars($transaction['id']) . "</td>
                        <td>" . htmlspecialchars($transaction['numero_compte']) . "</td>
                        <td>" . htmlspecialchars($transaction['type_transaction']) . "</td>
                        <td>" . htmlspecialchars($transaction['montant']) . "</td>
                        <td>" . htmlspecialchars($transaction['date_transaction']) . "</td>
                      </tr>";
            }
            echo "</tbody>
                  </table>";
        }
        ?>
    </div>
</body>
</html>
``` -->

#### 4. Gestion des Utilisateurs : Connexion et Déconnexion

**Étapes :**
- Créer un système de connexion pour les employés de la banque.
- Gérer les sessions pour maintenir l'état de connexion.
- Ajouter un bouton de déconnexion.

**Exemple de code :**
```php
// login.php
<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($client && password_verify($mot_de_passe, $client['mot_de_passe'])) {
        $_SESSION['client_id'] = $client['id'];
        header('Location: dashboard.php');
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body

>
    <div class="container">
        <h2 class="mt-4">Connexion</h2>
        <?php if (isset($erreur)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Connexion</button>
        </form>
    </div>
</body>
</html>

// logout.php
<?php
session_start();
session_destroy();
header('Location: login.php');
exit();
?>
```

#### 5. Espace Client : Personnalisation des Informations Personnelles

**Étapes :**
- Créer une page pour que les clients puissent modifier leurs informations personnelles.
- Utiliser des formulaires pour récupérer les nouvelles informations et les mettre à jour dans la base de données.

<!-- **Exemple de code :**
```php
// modifier_client.php
<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=banque', 'root', '');

if (!isset($_SESSION['client_id'])) {
    header('Location: login.php');
    exit();
}

$client_id = $_SESSION['client_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE clients SET nom = ?, prenom = ?, email = ? WHERE id = ?");
    $stmt->execute([$nom, $prenom, $email, $client_id]);
    $message = "Informations mises à jour avec succès.";
}

$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Informations Personnelles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="mt-4">Modifier Informations Personnelles</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form action="modifier_client.php" method="POST">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom :</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= htmlspecialchars($client['nom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom :</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?= htmlspecialchars($client['prenom']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($client['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
    </div>
</body>
</html>
``` -->

#### 6. Création de Fichiers Log

**Étapes :**
- Ajouter des fonctions pour enregistrer les actions importantes dans des fichiers log.
- Utiliser les fonctions PHP comme `file_put_contents` et `date` pour écrire dans les fichiers log.

<!-- **Exemple de code :**
```php
// fonctions.php
function logAction($message) {
    $logfile = 'logs/actions.log';
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "$timestamp - $message\n";
    file_put_contents($logfile, $logMessage, FILE_APPEND);
}

// Exemples d'utilisation dans les scripts existants
logAction("Client ID $client_id a modifié ses informations.");
logAction("Nouvelle transaction: $type de $montant sur le compte $compte_id.");
``` -->

### Conclusion

Ce TP vous offre une base solide pour la création d'une application bancaire en PHP, avec des améliorations possibles pour rendre l'application plus complète et fonctionnelle.