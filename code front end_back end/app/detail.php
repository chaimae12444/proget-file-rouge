<?php

session_start();

require('../config/database.php');

// Vérifier si l'ID du produit est présent dans l'URL
if (!isset($_GET['id'])) {
    header("Location: index.php"); 
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM product WHERE id_Product = ?");
$stmt->execute([$_GET['id']]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

// Si le produit n'existe pas, redirection vers index
if (!$produit) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title><?= htmlspecialchars($produit['name']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Barre de navigation -->
<nav class="navbar">
    <div class="nav-logo">
        <!-- Logo du site -->
        <img src="../assets/image/logo.jpg" alt="logo">
        <span>Parapharmacie</span>
    </div>

    <div class="nav-links">
        <!-- Lien vers la page d'accueil -->
        <a href="index.php">HOME</a>

        <!-- Lien vers le panier -->
        <a href="panier.php">🛒 Panier</a>

        <?php if (isset($_SESSION['idUser'])): ?>
           
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <!-- Si l'utilisateur n'est pas connecté -->
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</nav>

<!-- Section détails du produit -->
<section class="details">

    <!-- Image du produit -->
    <div class="details-img">
        <img src="../uploads/<?= htmlspecialchars($produit['image']) ?>" 
             alt="<?= htmlspecialchars($produit['name']) ?>">
    </div>

    <!-- Informations du produit -->
    <div class="details-info">

        <!-- Nom du produit -->
        <h1><?= htmlspecialchars($produit['name']) ?></h1>

        <!-- Prix du produit -->
        <p class="prix"><?= htmlspecialchars($produit['prix']) ?> DH</p>

        <!-- Description du produit -->
        <p><?= htmlspecialchars($produit['description']) ?></p>

        <!-- Bouton ajouter au panier -->
        <a href="panier.php?id=<?= $produit['id_Product'] ?>" class="btn">
            Ajouter au panier
        </a>

    </div>
</section>


<footer class="footer">
    <p>©️ 2026 Parapharmacie. Tous droits réservés.</p>
</footer>

</body>
</html>
