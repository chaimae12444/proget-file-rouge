<?php
session_start();
require  '../config/database.php';

// Protection admin
if (!isset($_SESSION['idUser']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../app/login.php");
    exit;
}

// Récupérer commandes
$commandes = $pdo->query("
    SELECT o.*,
     u.Name, u.email, 
      p.name as produit, p.prix, 
      c.Qte
    FROM orders o
    JOIN cart c ON o.idCart = c.idCart
    JOIN user u ON c.idUser = u.idUser
    JOIN product p ON c.id_Product = p.id_Product
    ORDER BY o.date_order DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Commandes</title>
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
    

    <table>
        <tr>
            <th>name</th>
            <th>Client</th>
            <th>Produit</th>
            <th>Qté</th>
            <th>Total</th>
            <th>Adresse</th>
            <th>Téléphone</th>
            <th>Date</th>
            <th>WhatsApp</th>
        </tr>
        <?php foreach ($commandes as $cmd): ?>
        <tr>
            
            <td>
                <?= htmlspecialchars($cmd['Name']) ?><br> </td>
               <td>  <small><?= htmlspecialchars($cmd['email']) ?></small></td>
            <td><?= htmlspecialchars($cmd['produit']) ?></td>
            <td><?= $cmd['Qte'] ?></td>
            <td><?= number_format($cmd['prix'] * $cmd['Qte'], 2) ?> DH</td>
            <td><?= htmlspecialchars($cmd['Adresse']) ?></td>
            <td><?= htmlspecialchars($cmd['Telephone']) ?></td>
            <td><?= $cmd['date_order'] ?></td>
            <td>
                <a href="https://wa.me/212<?= ltrim($cmd['Telephone'], '0') ?>?text=Bonjour <?= urlencode($cmd['Name']) ?>, votre commande de <?= urlencode($cmd['produit']) ?> est confirmée!" 
                   target="_blank" class="btn-whatsapp">
                    Contacter
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>