<?php
session_start(); // Démarrer la session
require('../config/database.php'); // Connexion à la base de données

$error = '';

if (isset($_POST['submit'])) { // Vérifier si le formulaire est soumis

    $email    = trim($_POST['email']); // Récupérer l'email
    $password = $_POST['password'];   // Récupérer le mot de passe

    // Chercher l'utilisateur par email
    $stmt = $pdo->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Email et mot de passe corrects → sauvegarder en session
        $_SESSION['idUser'] = $user['idUser'];
        $_SESSION['role']   = $user['role'];
        $_SESSION['name']   = $user['Name'];

        // Rediriger selon le rôle
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../app/index.php");
        }
        exit; // Arrêter le script après la redirection

    } else {
        $error = "Email ou mot de passe incorrect."; // Erreur de connexion
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <h2>Bienvenue</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
            <!-- Afficher l'erreur + protection contre XSS -->
        <?php endif; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" required placeholder="nom@exemple.com">

            <label>Mot de passe</label>
            <input type="password" name="password" required placeholder="••••••••">

            <button type="submit" name="submit">login →</button>
            <!-- name="submit" nécessaire pour que isset() fonctionne -->
        </form>

        <p>Pas encore de compte ? <a href="signup.php">S'inscrire</a></p>
    </div>
</body>
</html>