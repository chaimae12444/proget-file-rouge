<?php
session_start();
require('../config/database.php');

// Protection admin
if (!isset($_SESSION['idUser']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../app/login.php");
    exit;
}

// Compteurs
$nbProduits = $pdo->query("SELECT COUNT(*) FROM product")->fetchColumn();
$nbCommandes = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$nbClients = $pdo->query("SELECT COUNT(*) FROM user WHERE role = 'client'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Dashboard</title>
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
    <h1>Tableau de bord</h1>
    <p>Bonjour Admin </p>

    <div class="cards">
        <div class="card">
            <h3>Produits</h3>
            <span><?= $nbProduits ?></span>
        </div>
        <div class="card">
            <h3>Commandes</h3>
            <span><?= $nbCommandes ?></span>
        </div>
        <div class="card">
            <h3>Clients</h3>
            <span><?= $nbClients ?></span>
        </div>
    </div>
</div>

</body>
</html>