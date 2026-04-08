# Les Tableaux PHP

les_Tableaux_php.md 2023-11-23
Chapitre : Les Tableaux en PHP
Introduction
Types de Tableaux en PHP
Utilisation et Manipulation des Tableaux
Exercices
TP
Conclusion


## Introduction aux Tableaux en PHP

Définition d'un Tableau
En programmation, un tableau est une structure de données qui permet de stocker et d'organiser un
ensemble de valeurs sous un même nom. Ces valeurs, appelées éléments du tableau, peuvent être de
différents types tels que des nombres, des chaînes de caractères, des objets, etc.
En PHP, les tableaux peuvent être de deux types principaux : les tableaux à index numérique et les tableaux
associatifs.
Utilité des Tableaux
Les tableaux sont utilisés pour stocker des collections de données de manière organisée. Ils offrent une
solution efficace pour manipuler des ensembles de valeurs et sont essentiels pour de nombreuses tâches en
programmation.
Stockage de données multiples : Les tableaux permettent de stocker plusieurs valeurs sous une seule
variable.
Facilité de manipulation : Les tableaux offrent des fonctionnalités pour accéder, ajouter, supprimer et
modifier des éléments de manière simple et efficace.
Utilisation avec les boucles : Les tableaux s'intègrent bien avec les boucles, facilitant le traitement des
données en masse.
1 / 8

les_Tableaux_php.md 2023-11-23
Types de Tableaux en PHP
Tableaux à Index Numérique
Les tableaux à index numérique utilisent des indices numériques pour accéder à leurs éléments. L'indice
commence généralement à 0 et s'incrémente de 1 pour chaque nouvel élément.
Exemple :
$fruits = array("Pomme", "Banane", "Orange");
// ou
$fruits = ["Pomme", "Banane", "Orange"];
Dans cet exemple, $fruits est un tableau à index numérique avec les indices 0, 1, et 2.
Tableaux Associatifs
Les tableaux associatifs utilisent des clés (noms) pour accéder à leurs éléments plutôt que des indices
numériques.
Exemple :
$personne = array("nom" => "Doe", "prénom" => "John", "âge" => 30);
//ou
$personne = [
"nom" => "Doe",
"prénom" => "John",
"âge" => 30
];
Dans cet exemple, $personne est un tableau associatif avec des clés "nom", "prénom", et "âge".
Utilisation et Manipulation des Tableaux
l'accés aux éléments d'un tableau peut se faire en connaissance des indices ou cléss précis avec les quelles on
veut interagir ou de maniére classique avec les boucles
Accès aux Éléments
// Accéder à un élément dans un tableau à index numérique
echo $fruits[0]; // Affiche "Pomme"
// Accéder à un élément dans un tableau associatif
echo $personne["prénom"]; // Affiche "John"
Ajout d'Éléments
2 / 8

les_Tableaux_php.md 2023-11-23
// Ajouter un élément à la fin d'un tableau à index numérique
$fruits[] = "Kiwi";
// Ajouter un élément à un tableau associatif
$personne["ville"] = "Paris";
Parcours d'un Tableau
// Parcourir un tableau à index numérique
foreach ($fruits as $fruit) {
echo $fruit . " ";
}
// Parcourir un tableau associatif
foreach ($personne as $cle => $valeur) {
echo $cle . ": " . $valeur . " ";
}
Les tableaux en PHP offrent de nombreuses fonctions intégrées pour les utiliser et les manipuler. Voici
quelques exemples :
Suppression d'Éléments
// Supprimer un élément dans un tableau à index numérique
unset($fruits[1]);
// Supprimer un élément dans un tableau associatif
unset($personne["âge"]);
Taille d'un Tableau
// Taille d'un tableau à index numérique
$taille = count($fruits);
// Taille d'un tableau associatif
$taille_personne = count($personne);
Manipulation Avancée des Tableaux en PHP
Maintenant que nous avons une compréhension de base des tableaux en PHP, explorons quelques fonctions
intégrées pour une manipulation plus avancée.
3 / 8

les_Tableaux_php.md 2023-11-23
Fonctions pour la Manipulation des Tableaux
1. array_push et array_pop
Ces fonctions permettent d'ajouter des éléments à la fin d'un tableau (array_push) et de les retirer de la fin
(array_pop).
// Ajouter un élément à la fin du tableau
array_push($fruits, "Mangue");
// Retirer l'élément de la fin du tableau
$elementRetire = array_pop($fruits);
2. array_shift et array_unshift
Ces fonctions permettent d'ajouter des éléments au début d'un tableau (array_unshift) et de les retirer du
début (array_shift).
// Ajouter un élément au début du tableau
array_unshift($fruits, "Fraise");
// Retirer l'élément du début du tableau
$elementRetire = array_shift($fruits);
3. array_merge
La fonction array_merge fusionne deux tableaux en un seul.
$nouveauxFruits = array("Ananas", "Papaye");
$fruits = array_merge($fruits, $nouveauxFruits);
4. array_slice
array_slice extrait une partie d'un tableau.
$portion = array_slice($fruits, 1, 2); // Extrait les éléments de l'index 1 à 2
5. array_splice
array_splice supprime et remplace des éléments dans un tableau.
// Remplace deux éléments à partir de l'index 1
array_splice($fruits, 1, 2, "Raisin", "Pêche");
4 / 8

les_Tableaux_php.md 2023-11-23
6. array_reverse
array_reverse inverse l'ordre des éléments d'un tableau.
$fruitsInverses = array_reverse($fruits);
7. array_search
array_search recherche la clé d'une valeur dans un tableau.
$cle = array_search("Pomme", $fruits); // Retourne la clé de "Pomme"
8. in_array
in_array vérifie si une valeur existe dans un tableau.
$existe = in_array("Banane", $fruits); // Retourne true si "Banane" existe
9. array_keys et array_values
array_keys retourne les clés d'un tableau, et array_values retourne les valeurs.
$cles = array_keys($personne); // Retourne les clés du tableau associatif
$valeurs = array_values($personne); // Retourne les valeurs du tableau associatif
10. array_unique
array_unique supprime les doublons d'un tableau.
$fruitsUniques = array_unique($fruits);
Continuons notre exploration des tableaux en PHP avec des exemples plus avancés sur les tableaux.
Exemples Avancés
1. Tableau Multidimensionnel
Un tableau multidimensionnel est un tableau qui contient d'autres tableaux. Cela permet de représenter des
structures de données plus complexes.
5 / 8

les_Tableaux_php.md 2023-11-23
$personnes = array(
array("nom" => "Doe", "prénom" => "John", "âge" => 30),
array("nom" => "Smith", "prénom" => "Jane", "âge" => 25),
array("nom" => "Brown", "prénom" => "Bob", "âge" => 35)
);
// Accéder à un élément dans un tableau multidimensionnel
echo $personnes[0]["prénom"]; // Affiche "John"
2. Tri d'un Tableau
PHP offre des fonctions pour trier les tableaux. Par exemple, pour trier un tableau de nombres de manière
croissante :
$chiffres = array(5, 2, 8, 1, 9);
sort($chiffres); // Trie le tableau par ordre croissant
3. Filtrage d'un Tableau
La fonction array_filter permet de filtrer les éléments d'un tableau en fonction d'une fonction de rappel.
$ages = array(25, 30, 18, 42, 22);
$adultes = array_filter($ages, function ($age) {
return $age >= 18;
});
Exercices
Exercice 1 : Ajout et Suppression
Ajoutez deux fruits au tableau $fruits et supprimez un fruit. Affichez le tableau résultant.
$fruits = array("Pomme", "Banane", "Orange");
array_push($fruits, "Kiwi", "Melon");
unset($fruits[1]);
print_r($fruits);
Exercice 2 : Tableau Associatif
Créez un tableau associatif représentant une voiture avec des informations telles que la marque, le modèle, et
l'année. Affichez ces informations.
6 / 8

les_Tableaux_php.md 2023-11-23
$voiture = array(
"marque" => "Toyota",
"modèle" => "Corolla",
"année" => 2022
);
foreach ($voiture as $cle => $valeur) {
echo $cle . ": " . $valeur . "<br>";
}
Travaux Pratiques (TP)
TP 1 : Gestion de Stock
Créez un tableau représentant un stock de produits. Chaque produit doit avoir un nom, un prix, et une
quantité en stock. Réalisez les opérations suivantes :
Ajoutez un nouveau produit au stock.
Mettez à jour la quantité d'un produit existant.
Affichez le stock complet.
TP 2 : Calcul de Moyenne
Créez un tableau de notes d'étudiants. Calculez la moyenne des notes et affichez-la.
TP 3 : Système de Votes
Créez un tableau représentant les votes pour un concours. Comptez le nombre de votes pour chaque
candidat et affichez le gagnant.
TP 4 : Gestion de Contacts
Créez un tableau représentant une liste de contacts. Chaque contact doit avoir un nom, un numéro de
téléphone, et une adresse email. Réalisez les opérations suivantes :
Ajoutez un nouveau contact.
Modifiez l'adresse email d'un contact existant.
Affichez la liste complète des contacts.
TP 5 : Jeu de Morpion
Créez un tableau à deux dimensions représentant un plateau de jeu de Morpion. Réalisez les opérations
suivantes :
Affichez le plateau de jeu.
Permettez à un joueur de placer son symbole (X ou O) sur le plateau.
Vérifiez s'il y a un gagnant.
Ces travaux pratiques aideront à renforcer votre compréhension des tableaux en PHP et de leurs applications
pratiques. N'hésitez pas à expérimenter et à personnaliser ces exercices pour améliorer vos compétences.
7 / 8

les_Tableaux_php.md 2023-11-23
Conclusion
En résumé, ce cours sur les tableaux en PHP a mis en lumière l'importance de cette structure de données dans
le développement web. Nous avons exploré la création, la manipulation, et les opérations courantes sur les
tableaux indexés, associatifs, et multidimensionnels.
Les tableaux offrent une flexibilité cruciale pour organiser et gérer les données de manière dynamique.
Comprendre leur utilisation permet d'optimiser la structure des informations dans le développement web.
Pratiquez régulièrement l'utilisation des tableaux pour renforcer vos compétences et vous préparer à des défis
plus avancés. Les tableaux sont un outil essentiel pour les développeurs PHP, et une maîtrise approfondie sera
bénéfique dans votre parcours professionnel.
8 / 8


---

## Exercices pratiques

> Mets en pratique ce que tu viens d'apprendre avant de passer au chapitre suivant.

### Exercice 1 — Application directe

Applique les notions vues sur un exemple concret de ton choix.

**Consigne :** Réalise l'exercice correspondant à ce chapitre et valide ta compréhension avant de continuer.

### Exercice 2 — Questions de compréhension

1. Quel est le concept fondamental de ce chapitre ?
2. Cite un cas d'usage concret en contexte professionnel.
3. Quelle notion t'a semblé la plus complexe ? Comment l'as-tu abordée ?

### Checklist avant de continuer

- [ ] J'ai lu et compris le contenu du chapitre
- [ ] J'ai réalisé au moins un exercice pratique
- [ ] Je peux résumer ce chapitre en 3 points essentiels
