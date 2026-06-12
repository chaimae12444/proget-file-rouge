<?php

session_start();


require('../config/database.php');


if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

// Récupérer l'ID de l'utilisateur connecté
$idUser = $_SESSION['idUser'];


// Vérifier si un produit est envoyé via URL
if (isset($_GET['id'])) {

    // Vérifier si le produit existe déjà dans le panier
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE idUser = ? AND id_Product = ?");
    $stmt->execute([$idUser, $_GET['id']]);

    if ($stmt->fetch()) {
        // Si le produit existe déjà → augmenter la quantité
        $pdo->prepare("
            UPDATE cart 
            SET Qte = Qte + 1 
            WHERE idUser = ? AND id_Product = ?
        ")->execute([$idUser, $_GET['id']]);

    } else {
        // Sinon → ajouter nouveau produit dans le panier
        $pdo->prepare("
            INSERT INTO cart (Qte, idUser, id_Product) 
            VALUES (1, ?, ?)
        ")->execute([$idUser, $_GET['id']]);
    }

    // Redirection vers le panier après ajout
    header("Location: panier.php");
    exit;
}




if (isset($_GET['delete'])) {

    // ID du panier à supprimer
    $idCart = (int)$_GET['delete'];

    // Vérifier si ce panier est lié à une commande
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE idCart = ?");
    $stmt->execute([$idCart]);

    // Si pas utilisé dans une commande → autoriser suppression
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("
            DELETE FROM cart 
            WHERE idCart = ? AND idUser = ?
        ")->execute([$idCart, $idUser]);
    }

    // Redirection après suppression
    header("Location: panier.php");
    exit;
}




// Récupérer les produits du panier avec leurs infos produit
$stmt = $pdo->prepare("
    SELECT c.*,
     p.name, p.prix, p.image 
    FROM cart c 
    JOIN product p ON c.id_Product = p.id_Product 
    WHERE c.idUser = ?
");

$stmt->execute([$idUser]);

// Liste des produits du panier
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Calcul du total du panier
$total = array_sum(array_map(fn($i) => $i['prix'] * $i['Qte'], $panier));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-logo">
        <img src="../assets/css/image/logo.jpg" alt="logo">
        <span>Parapharmacie</span>
    </div>

    <div class="nav-links">
        <a href="index.php">HOME</a>
        <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<!-- Contenu panier -->
<div class="panier-container">

    <h2>Mon Panier</h2>

    <?php if (empty($panier)): ?>
        <!-- Cas panier vide -->
        <p>Panier vide. <a href="index.php">Continuer les achats</a></p>

    <?php else: ?>

        <!-- Affichage des articles du panier -->
        <?php foreach ($panier as $item): ?>
        <div class="panier-item">

            <!-- Image produit -->
            <img src="../uploads/<?= htmlspecialchars($item['image']) ?>" height="80">

            <!-- Nom produit -->
            <span><?= htmlspecialchars($item['name']) ?></span>

            <!-- Quantité et prix -->
            <span><?= $item['Qte'] ?> x <?= $item['prix'] ?> DH</span>

            <!-- Supprimer du panier -->
            <a href="?delete=<?= $item['idCart'] ?>">🗑️</a>

        </div>
        <?php endforeach; ?>

        <!-- Total -->
        <div class="panier-total">
            <p>Total : <strong><?= number_format($total, 2) ?> DH</strong></p>
            <p>Livraison : <strong>OFFERTE</strong></p>

            <!-- Bouton commande -->
            <a href="confirmation.php" class="btn">Commander →</a>
        </div>

    <?php endif; ?>

</div>

<!-- Footer -->
<footer class="footer">
    <p>©️ 2026 Parapharmacie. Tous droits réservés.</p>
</footer>

</body>
</html>