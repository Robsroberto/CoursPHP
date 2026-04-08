### **TP : Gestion des données d’un formulaire avec GET et POST en PHP**

Ce TP vous permettra de comprendre le fonctionnement des formulaires HTML, l'utilisation des attributs `name` et `method`, ainsi que le traitement des données en PHP avec les méthodes `GET` et `POST`. Nous aborderons également les bonnes pratiques, comme la vérification si le bouton a été appuyé et la sécurisation des données saisies.

---

### **Objectifs :**
1. Comprendre l'importance de l'attribut `name` dans les champs de formulaire.
2. Différencier les méthodes `GET` et `POST`.
3. Savoir vérifier si un formulaire a été soumis.
4. Appliquer les fonctions de base pour sécuriser les données (e.g. `trim()`, `htmlspecialchars()`).

---

### **Partie 1 : Préparation du formulaire HTML**

Créez un fichier `formulaire.html` contenant un formulaire simple avec différents champs.

```html
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulaire Exemple</title>
</head>
<body>
<h1>Formulaire de Contact</h1>
<form action="traitement_formulaire.php" method="POST">
<!-- Nom de l'utilisateur -->
<label for="nom">Nom :</label>
<input type="text" id="nom" name="nom" placeholder="Votre nom" required><br><br>

<!-- Email de l'utilisateur -->
<label for="email">Email :</label>
<input type="email" id="email" name="email" placeholder="Votre email" required><br><br>

<!-- Message -->
<label for="message">Message :</label><br>
<textarea id="message" name="message" placeholder="Votre message" rows="5" cols="30" required></textarea><br><br>

<!-- Bouton de soumission -->
<button type="submit" name="submit">Envoyer</button>
</form>
</body>
</html>
```

---

### **Partie 2 : Comprendre l'attribut `name`**

1. **Pourquoi utiliser `name` ?**
- L'attribut `name` associe une donnée saisie dans un champ HTML à une clé dans les superglobales `$_GET` ou `$_POST`.
- Par exemple, dans le formulaire ci-dessus, `name="nom"` signifie que la valeur saisie dans le champ sera accessible via `$_POST['nom']`.

2. **Utilisation sans `name` :**
- Sans `name`, les données du champ ne sont pas envoyées au serveur.

---

### **Partie 3 : Traitement des données en PHP avec POST**

Créez un fichier `traitement_formulaire.php` pour récupérer les données du formulaire via `POST`.

#### **Code PHP avec vérification du bouton submit :**
```php
<?php
if (isset($_POST['submit'])) {
    // Récupérer et sécuriser les données
    $nom = trim(htmlspecialchars($_POST['nom']));
    $email = trim(htmlspecialchars($_POST['email']));
    $message = trim(htmlspecialchars($_POST['message']));

    // Vérifier si les champs sont vides
    if (empty($nom) || empty($email) || empty($message)) {
        echo "Veuillez remplir tous les champs !";
    } else {
        echo "<h2>Vos données :</h2>";
        echo "Nom : $nom<br>";
        echo "Email : $email<br>";
        echo "Message : $message<br>";
    }
} else {
    echo "Le formulaire n'a pas été soumis correctement.";
}
?>
```

---

### **Partie 4 : Traitement des données en PHP avec GET**

Modifiez le formulaire pour utiliser la méthode `GET` dans un fichier `formulaire_get.html`.

#### **Formulaire GET :**
```html
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Formulaire GET Exemple</title>
</head>
<body>
<h1>Formulaire de Contact (GET)</h1>
<form action="traitement_formulaire_get.php" method="GET">
<label for="nom">Nom :</label>
<input type="text" id="nom" name="nom" placeholder="Votre nom" required><br><br>

<label for="email">Email :</label>
<input type="email" id="email" name="email" placeholder="Votre email" required><br><br>

<label for="message">Message :</label><br>
<textarea id="message" name="message" placeholder="Votre message" rows="5" cols="30" required></textarea><br><br>

<button type="submit" name="submit">Envoyer</button>
</form>
</body>
</html>
```

#### **Traitement GET :**
Créez un fichier `traitement_formulaire_get.php` :

```php
<?php
if (isset($_GET['submit'])) {
    // Récupérer et sécuriser les données
    $nom = trim(htmlspecialchars($_GET['nom']));
    $email = trim(htmlspecialchars($_GET['email']));
    $message = trim(htmlspecialchars($_GET['message']));

    // Vérifier si les champs sont vides
    if (empty($nom) || empty($email) || empty($message)) {
        echo "Veuillez remplir tous les champs !";
    } else {
        echo "<h2>Vos données :</h2>";
        echo "Nom : $nom<br>";
        echo "Email : $email<br>";
        echo "Message : $message<br>";
    }
} else {
    echo "Le formulaire n'a pas été soumis correctement.";
}
?>
```

---

### **Différence entre GET et POST :**

1. **GET :**
- Les données sont envoyées dans l'URL (visible dans la barre d'adresse).
- Utilisé pour des requêtes simples (recherche, navigation).
- Limité en taille (taille maximale de l'URL).
- Pas sécurisé pour transmettre des données sensibles.

2. **POST :**
- Les données sont envoyées dans le corps de la requête HTTP (non visible dans l'URL).
- Utilisé pour des formulaires complexes ou pour envoyer des données sensibles.
- Pas de limite de taille pratique pour les données envoyées.

---

### **Partie 5 : Exercices pratiques**

#### **Exercice 1 : Formulaire de connexion**
- Créez un formulaire avec deux champs : `username` et `password`.
- Récupérez les données avec `POST` et affichez un message :
- Si le champ `username` est vide, affichez : "Veuillez entrer votre nom d'utilisateur."
- Si le champ `password` est vide, affichez : "Veuillez entrer votre mot de passe."
- Sinon, affichez : "Connexion réussie !"

#### **Exercice 2 : Formulaire de recherche (GET)**
- Créez un formulaire avec un seul champ `query` pour permettre à l'utilisateur de rechercher quelque chose.
- Affichez la recherche dans l'URL (grâce à `GET`) et sur la page.

#### **Exercice 3 : Vérification avancée**
- Modifiez le formulaire de contact pour :
- Vérifier que l'email contient un "@" valide avec la fonction `filter_var($email, FILTER_VALIDATE_EMAIL)`.
- Si l'email n'est pas valide, affichez : "Email invalide !"
- Si tout est correct, affichez les données.

---

### **Conclusion**

Ce TP permet de :
1. Comprendre le rôle essentiel de l'attribut `name` dans les champs HTML.
2. Différencier les méthodes `GET` et `POST` en PHP.
3. Appliquer les bonnes pratiques comme la vérification du bouton submit (`isset`) et la sécurisation des données (`trim`, `htmlspecialchars`).
4. Réaliser des exercices pratiques pour renforcer leurs compétences.