<?php

session_start();

// Connexion à la base de données
require('../config/database.php');

if (!isset($_SESSION['idUser'])) {
    header("Location: login.php");
    exit;
}

// Récupérer l'ID utilisateur connecté
$idUser = $_SESSION['idUser'];



// Vérifier si le formulaire est soumis
if (isset($_POST['submit'])) {

    $adresse = trim($_POST['adresse']);
    $telephone = trim($_POST['telephone']);

    // Récupérer les articles du panier de l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM cart WHERE idUser = ?");
    $stmt->execute([$idUser]);
    $panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insérer chaque produit du panier dans la table orders
    foreach ($panier as $item) {
        $pdo->prepare("
            INSERT INTO orders (date_order, Adresse, Telephone, idCart) 
            VALUES (NOW(), ?, ?, ?)
        ")->execute([$adresse, $telephone, $item['idCart']]);
    }

    // Message WhatsApp avec les informations de commande
    $message = "Bonjour, commande passée. Adresse: $adresse. Tél: $telephone.";

    // Redirection vers WhatsApp avec message encodé
    header("Location: https://wa.me/212600000000?text=" . urlencode($message));
    exit;
}




// Récupérer le panier avec les informations des produits
$stmt = $pdo->prepare("
    SELECT c.*,
     p.name, p.prix 
    FROM cart c 
    JOIN product p ON c.id_Product = p.id_Product 
    WHERE c.idUser = ?
");

$stmt->execute([$idUser]);

// Liste des produits dans le panier
$panier = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcul du total général du panier
$total = array_sum(array_map(fn($i) => $i['prix'] * $i['Qte'], $panier));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="nav-logo">
        <img src="../assets/image/logo.jpg" alt="logo">
        <span>Parapharmacie</span>
    </div>

    <div class="nav-links">
        <a href="index.php">HOME</a>
        <a href="panier.php">🛒 Panier</a>
    </div>
</nav>

<!-- Page confirmation -->
<div class="confirmation-container">

    <h2>Finaliser la commande</h2>

    <!-- Formulaire de commande -->
    <form method="POST">

        <!-- Adresse de livraison -->
        <input type="text" name="adresse" placeholder="Adresse de livraison" required>

        <!-- Téléphone client -->
        <input type="text" name="telephone" placeholder="Téléphone" required>

        <!-- Récapitulatif du panier -->
        <div class="recap">

            <?php foreach ($panier as $item): ?>
                <p>
                    <?= htmlspecialchars($item['name']) ?> 
                    x<?= $item['Qte'] ?> — 
                    <?= number_format($item['prix'] * $item['Qte'], 2) ?> DH
                </p>
            <?php endforeach; ?>

            <!-- Livraison -->
            <p>Livraison : <strong>OFFERTE</strong></p>

            <!-- Total général -->
            <p>Total : <strong><?= number_format($total, 2) ?> DH</strong></p>

        </div>

        <!-- Bouton validation commande -->
        <button type="submit" name="submit">
            Commander via WhatsApp 
        </button>

    </form>
</div>

<!-- Footer -->
<footer class="footer">
    <p>©️ 2026 Parapharmacie. Tous droits réservés.</p>
</footer>

</body>
</html>
