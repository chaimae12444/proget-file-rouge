<?php

session_start();

// Connexion à la base de données
require('../config/database.php');

// Vérifier si l'utilisateur est connecté et s'il est admin
if (!isset($_SESSION['idUser']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../app/login.php");
    exit;
}

// Messages de retour
$success = '';
$error = '';



// Vérifier si le formulaire d'ajout est soumis
if (isset($_POST['submit'])) {

    // Récupérer les données du formulaire
    $name = trim($_POST['name']);
    $prix = trim($_POST['prix']);
    $description = trim($_POST['description']);
    $idCategorie = $_POST['idCategorie'];

   
    $image = '';

    // Vérifier si une image est envoyée
    if ($_FILES['image']['name'] != '') {

        // Créer un nom unique pour éviter les conflits
        $image = time() . '_' . $_FILES['image']['name'];

        // Déplacer l'image vers le dossier uploads
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }

    // Insérer le produit dans la base de données
    $stmt = $pdo->prepare("
        INSERT INTO product (name, description, prix, idCategorie, image) 
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([$name, $description, $prix, $idCategorie, $image]);

    // Message de succès
    $success = "Produit ajouté avec succès !";
}


if (isset($_GET['delete'])) {

    // ID du produit à supprimer
    $id = $_GET['delete'];

    // 1. Supprimer les commandes liées au produit via cart
    $stmt = $pdo->prepare("
        DELETE FROM orders 
        WHERE idCart IN (
            SELECT idCart FROM cart WHERE id_Product = ?
        )
    ");
    $stmt->execute([$id]);

    // 2. Supprimer le produit du panier
    $stmt = $pdo->prepare("
        DELETE FROM cart WHERE id_Product = ?
    ");
    $stmt->execute([$id]);

    // 3. Supprimer le produit lui-même
    $stmt = $pdo->prepare("
        DELETE FROM product WHERE id_Product = ?
    ");
    $stmt->execute([$id]);

    // Redirection après suppression
    header("Location: produits.php");
    exit;
}




// Récupérer tous les produits avec leur catégorie
$produits = $pdo->query("
    SELECT p.*,
     c.name as categorie 
    FROM product p 
    JOIN categorie c ON p.idCategorie = c.idCategorie
")->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les catégories
$categories = $pdo->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Produits</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- NAVBAR ADMIN -->
<nav class="admin-nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="produits.php">Produits</a>
    <a href="commandes.php">Commandes</a>
    <a href="../app/logout.php">Déconnexion</a>
</nav>

<div class="admin-container">

    <h2>Gestion des Produits</h2>

    <!-- Message succès -->
    <?php if ($success): ?>
       
    <?php endif; ?>

    <div class="form-section">

        <h3>Ajouter un produit</h3>

        <form method="POST" enctype="multipart/form-data">

            <!-- Nom produit -->
            <input type="text" name="name" placeholder="Nom du produit" required>

            <!-- Prix -->
            <input type="number" name="prix" placeholder="Prix (DH)" step="0.01" required>

            <!-- Catégorie -->
            <select name="idCategorie" required>
                <option value="">-- Catégorie --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['idCategorie'] ?>">
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Description -->
            <textarea name="description" placeholder="Description du produit"></textarea>

            <!-- Image -->
            <input type="file" name="image" accept="image/*">

            <!-- Bouton submit -->
            <button type="submit" name="submit">Ajouter le produit</button>

        </form>
    </div>


    <h3>Liste des produits (<?= count($produits) ?>)</h3>

    <table>

        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Prix</th>
            <th>Catégorie</th>
            <th>Actions</th>
        </tr>

        <!-- Boucle des produits -->
        <?php foreach ($produits as $p): ?>
        <tr>

            <!-- Image produit -->
            <td>
                <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" height="50">
            </td>

            <!-- Nom -->
            <td><?= htmlspecialchars($p['name']) ?></td>

            <!-- Prix -->
            <td><?= $p['prix'] ?> DH</td>

            <!-- Catégorie -->
            <td><?= htmlspecialchars($p['categorie']) ?></td>

            <!-- Actions -->
            <td>
                <!-- Modifier produit -->
                <a href="modifier.php?id=<?= $p['id_Product'] ?>">Modifier</a>

                <!-- Supprimer produit -->
                <a href="?delete=<?= $p['id_Product'] ?>" 
                   onclick="return confirm('Supprimer ?')">
                   Supprimer
                </a>
            </td>

        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>