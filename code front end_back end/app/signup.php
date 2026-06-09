<?php
// Démarrage de la session
session_start();

// Connexion à la base de données
require ('../config/database.php');

// Variables pour les messages d'erreur et de succès
$error = '';


if (isset($_POST['submit'])) {

    // Récupération des données du formulaire
    $name = trim($_POST['name']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérification si l'email existe déjà
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetch();

    // Si l'email est déjà utilisé
    if ($exists) {
        $error = "Cet email est déjà utilisé.";
    } else {

        // Hachage du mot de passe pour la sécurité
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Insertion du nouvel utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO user (Name, email, password, role) VALUES (?, ?, ?, 'client')");
        $stmt->execute([$name, $email, $hashed]);

      

        // Redirection vers la page de connexion
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>

    <!-- Fichier CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">

        <!-- Titre de la page -->
        <h2>Bienvenue</h2>

        <!-- Texte de bienvenue -->
        <p>Créez votre compte pour accéder à notre espace santé.</p>

        <!-- Affichage du message d'erreur -->
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <!-- Formulaire d'inscription -->
        <form method="POST">

            <!-- Champ Nom -->
            <label>Nom</label>
            <input type="text" name="name" required placeholder="Nom">

            <!-- Champ Email -->
            <label>Email</label>
            <input type="email" name="email" required placeholder="nom@exemple.com">

            <!-- Champ Mot de passe -->
            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">

            <!-- Bouton d'inscription -->
            <button type="submit" name="submit">signup →</button>
        </form>

        <!-- Lien vers la page de connexion -->
        <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>

    </div>
</body>
</html>