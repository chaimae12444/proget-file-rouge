<?php
// Démarrer la session
session_start();


require('../config/database.php');

// Récupérer tous les produits depuis la base de données
$produits = $pdo->query("SELECT * FROM product")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

   
    <title>Parapharmacie</title>

    <link rel="stylesheet" href="../assets/css/style.css">

   
       
</head>
<body>

<!-- ===== BARRE DE NAVIGATION ===== -->
<nav class="navbar">

    <!-- Logo et nom du site -->
    <div class="nav-logo">
        <img src="../assets/css/image/logo.jpg" alt="logo">
        <span>Parapharmacie</span>
    </div>

    <!-- Formulaire de recherche -->
    <div class="nav-search">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Rechercher un produit...">
            <button type="submit">🔍</button>
        </form>
    </div>

    <!-- Liens de navigation -->
    <div class="nav-links">
        <a href="panier.php">🛒 Panier</a>

        <?php if (isset($_SESSION['idUser'])): ?>
            
            <!-- Afficher Déconnexion si l'utilisateur est connecté -->
            <a href="logout.php">Déconnexion</a>

        <?php else: ?>

            <!-- Afficher Login et Signup si l'utilisateur n'est pas connecté -->
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>

        <?php endif; ?>
    </div>

</nav>

<!-- ===== IMAGE PRINCIPALE ===== -->
<header class="hero">

    <!-- Bannière du site -->
    <img src="../assets/css/image/header.png" alt="hero" class="hero-img">

</header>

<!-- ===== SECTION PRODUITS ===== -->
<section class="produits">

    <!-- Titre de la section -->
    <h2>Nos Essentiels</h2>

    <!-- Description -->
    <p>Recommandés par nos experts en santé.</p>

    <div class="grid">

        <!-- Parcourir tous les produits -->
        <?php foreach ($produits as $p): ?>

        <div class="card">

            <!-- Afficher l'image du produit -->
            <img src="../uploads/<?= htmlspecialchars($p['image']) ?>"
                 alt="<?= htmlspecialchars($p['name']) ?>">

            <!-- Afficher le nom du produit -->
            <h3><?= htmlspecialchars($p['name']) ?></h3>

            <!-- Afficher le prix du produit -->
            <p class="prix">
                <?= htmlspecialchars($p['prix']) ?> DH
            </p>

            <!-- Lien vers la page détail du produit -->
            <a href="../app/detail.php?id=<?= $p['id_Product'] ?>" class="btn">
                Voir
            </a>

        </div>

        <?php endforeach; ?>

    </div>

</section>

<footer class="footer">

    <p>©️ 2026 Parapharmacie. Tous droits réservés.</p>

</footer>

</body>
</html>