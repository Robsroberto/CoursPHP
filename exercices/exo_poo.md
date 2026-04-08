D'accord ! On va ajouter du **JavaScript (JS)** pour **dynamiser le formulaire**, en effectuant des **vérifications en temps réel** et en affichant les erreurs **sans recharger la page**.  

### **🎯 Objectifs**  
✅ **Empêcher l’envoi du formulaire si les champs sont invalides**  
✅ **Afficher des messages d'erreur dynamiques** sous chaque champ  
✅ **Mettre en surbrillance les champs incorrects**  
✅ **Utiliser PHP en backup** pour éviter toute triche côté client  

---

## **1️⃣ Mise à Jour du Formulaire HTML**  
Ajoutons des `div` pour afficher les erreurs sous chaque champ.  

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Dynamique avec JS & PHP</title>
    <style>
        .error { color: red; font-size: 14px; }
        .input-error { border: 2px solid red; }
    </style>
</head>
<body>

    <h2>Entrez vos informations</h2>
    <form id="personneForm" action="traitement.php" method="POST">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" required>
        <div class="error" id="error-nom"></div>
        <br><br>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" required>
        <div class="error" id="error-prenom"></div>
        <br><br>

        <label for="age">Âge :</label>
        <input type="number" name="age" id="age" required>
        <div class="error" id="error-age"></div>
        <br><br>

        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>
        <div class="error" id="error-email"></div>
        <br><br>

        <button type="submit">Envoyer</button>
    </form>

    <script src="script.js"></script>

</body>
</html>
```

✅ **Ajout de `div class="error"` sous chaque champ** pour afficher les messages d'erreur dynamiques.  
✅ **Ajout de `id="error-nom"`, `id="error-age"`, etc.** pour les cibler avec JS.  

---

## **2️⃣ JavaScript Dynamique (script.js)**
On écoute les événements de saisie (`input`) et on vérifie en temps réel les erreurs.

```javascript
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("personneForm");

    form.addEventListener("submit", function (event) {
        let isValid = true;

        // Récupération des champs
        const nom = document.getElementById("nom");
        const prenom = document.getElementById("prenom");
        const age = document.getElementById("age");
        const email = document.getElementById("email");

        // Récupération des div d'erreur
        const errorNom = document.getElementById("error-nom");
        const errorPrenom = document.getElementById("error-prenom");
        const errorAge = document.getElementById("error-age");
        const errorEmail = document.getElementById("error-email");

        // Reset erreurs
        errorNom.textContent = "";
        errorPrenom.textContent = "";
        errorAge.textContent = "";
        errorEmail.textContent = "";

        nom.classList.remove("input-error");
        prenom.classList.remove("input-error");
        age.classList.remove("input-error");
        email.classList.remove("input-error");

        // Validation du nom
        if (nom.value.trim().length < 2) {
            errorNom.textContent = "Le nom doit contenir au moins 2 lettres.";
            nom.classList.add("input-error");
            isValid = false;
        }

        // Validation du prénom
        if (prenom.value.trim().length < 2) {
            errorPrenom.textContent = "Le prénom doit contenir au moins 2 lettres.";
            prenom.classList.add("input-error");
            isValid = false;
        }

        // Validation de l'âge
        if (isNaN(age.value) || age.value < 1 || age.value > 120) {
            errorAge.textContent = "L'âge doit être entre 1 et 120.";
            age.classList.add("input-error");
            isValid = false;
        }

        // Validation de l'email
        const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        if (!emailPattern.test(email.value)) {
            errorEmail.textContent = "L'email est invalide.";
            email.classList.add("input-error");
            isValid = false;
        }

        // Si une erreur est détectée, on bloque l'envoi du formulaire
        if (!isValid) {
            event.preventDefault();
        }
    });
});
```

✅ **Écoute du formulaire sur `submit`** pour vérifier les erreurs **avant l’envoi**.  
✅ **Validation instantanée** sur le **nom, prénom, âge et email**.  
✅ **Ajout de la classe `input-error`** pour mettre en surbrillance les champs incorrects.  
✅ **Empêche l’envoi (`event.preventDefault()`) si des erreurs sont détectées**.  

---

## **3️⃣ Vérification Serveur avec PHP (traitement.php)**
Même si JS bloque les erreurs, un utilisateur peut **désactiver JS**. On doit donc **vérifier aussi en PHP**.

```php
<?php
include 'Personne.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Récupération des valeurs POST
        $nom = $_POST["nom"] ?? "";
        $prenom = $_POST["prenom"] ?? "";
        $age = $_POST["age"] ?? "";
        $email = $_POST["email"] ?? "";

        // Création de l'objet Personne avec validation
        $personne = new Personne($nom, $prenom, $age, $email);

        // Affichage des informations
        echo "<h2>Informations Enregistrées :</h2>";
        echo "<p>" . $personne->afficherInfos() . "</p>";

    } catch (Exception $e) {
        // Affichage des erreurs PHP
        echo "<p style='color:red;'>" . $e->getMessage() . "</p>";
    }
} else {
    echo "Aucune donnée reçue.";
}
?>
```

---

## **4️⃣ Explication du Code**
### **🚀 JavaScript (JS)**
- **JS bloque les erreurs AVANT l’envoi** pour éviter une requête inutile.  
- **JS affiche des erreurs dynamiques sous chaque champ** (évite un rechargement).  
- **JS empêche le formulaire d’être soumis si une erreur est présente**.  

### **🔐 PHP**
- **PHP fait une dernière vérification en cas de triche**.  
- **PHP empêche l’injection de mauvaises valeurs**.  
- **PHP stocke les données proprement si elles sont valides**.  

---

## **5️⃣ Résumé**
| 🛠 **Élément** | 🚀 **Rôle** |
|--------------|----------|
| **JavaScript (JS)** | Vérification **instantanée** et blocage si erreur **avant envoi** |
| **CSS** | Ajout de styles pour mettre en **surbrillance** les champs incorrects |
| **PHP (`throw new Exception`)** | Vérification **côté serveur** pour bloquer les valeurs invalides |

---

## **6️⃣ Exercice Bonus 💡**
1️⃣ **Ajoutez un champ "Mot de Passe"** avec JS qui empêche **les mots de passe trop courts (< 6 caractères)**.  
2️⃣ **Ajoutez un champ "Téléphone"** avec une **vérification du format international (`+33 6 12 34 56 78`)**.  
3️⃣ **Ajoutez une vérification que l'email est unique** en AJAX (requête serveur en temps réel).  

---

## **📢 Conclusion**
Avec **JS**, **CSS**, et **PHP**, on crée une **expérience fluide**, **sécurisée**, et **rapide** pour l’utilisateur.  

✅ **Tu veux que j'ajoute une base de données pour enregistrer les infos ?** 🔥