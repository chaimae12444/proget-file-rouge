<?php
session_start();
require('../config/database.php');

if (!isset($_SESSION['idUser']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../app/login.php");
    exit;
}

$success = '';
// Récupérer l'ID du produit
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer les informations du produit
$stmt = $pdo->prepare("SELECT * FROM product WHERE id_Product = ?");
$stmt->execute([$id]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header("Location: produits.php");
    exit;
}


if (isset($_POST['submit'])) {
    $name        = trim($_POST['name']);
    $prix        = trim($_POST['prix']);
    $description = trim($_POST['description']);
    $idCategorie = $_POST['idCategorie'];
    $image       = $produit['image'];

    if ($_FILES['image']['name'] != '') {
        $image = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image);
    }
   // Mettre à jour le produit
    $stmt = $pdo->prepare("UPDATE product SET name=?, description=?, prix=?, idCategorie=?, image=? WHERE id_Product=?");
    $stmt->execute([$name, $description, $prix, $idCategorie, $image, $id]);
    $success = "Produit modifié avec succès !";

    // Actualiser les données après modification
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id_Product = ?");
    $stmt->execute([$id]);
    $produit = $stmt->fetch(PDO::FETCH_ASSOC);
}
// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categorie")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Produit</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="admin-nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="produits.php">Produits</a>
    <a href="commandes.php">Commandes</a>
    <a href="../app/logout.php">Déconnexion</a>
</nav>

<div class="modifier-container">
    <h2> Modifier le produit</h2>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label class="label">Nom</label>
        <input type="text" name="name" value="<?= htmlspecialchars($produit['name']) ?>" required>

        <label class="label">Prix (DH)</label>
        <input type="number" name="prix" value="<?= $produit['prix'] ?>" step="0.01" required>

        <label class="label">Catégorie</label>
        <select name="idCategorie" required>
            <?php foreach ($categories as $c): ?>
                <option value="<?= $c['idCategorie'] ?>"
                    <?= $c['idCategorie'] == $produit['idCategorie'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label class="label">Description</label>
        <textarea name="description"><?= htmlspecialchars($produit['description']) ?></textarea>

        <label class="label">Image</label>
        <?php if ($produit['image']): ?>
            <div class="current-img">
                <img src="../uploads/<?= $produit['image'] ?>" height="60">
                <span>Laissez vide pour garder l'image actuelle</span>
            </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">

        <div class="form-actions">
            <button type="submit" name="submit" class="btn-save">💾 Enregistrer</button>
            <a href="produits.php" class="btn-cancel">✕ Annuler</a>
        </div>

    </form>
</div>

</body>
</html>