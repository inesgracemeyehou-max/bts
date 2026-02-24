<?php
// index.php
session_start();
if(isset($_SESSION['user_id'])) {
    // Rediriger vers le dashboard approprié si déjà connecté
    switch($_SESSION['role']) {
        case 'Admin': header("Location: Pages/DashboardAdmin.php"); break;
        case 'RH': header("Location: Pages/DashboardRH.php"); break;
        case 'Manager': header("Location: Pages/DashboardManager.php"); break;
        case 'Employé': header("Location: Pages/DashboardEmploye.php"); break;
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style1.css">
    <title>Connexion - Gestion RH</title>
</head>
<body>
    <header>
        <h1>Système de Gestion RH - SoftSec</h1>
    </header>

    <div class="container">
        <div class="card">
            <h2>Connexion</h2>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    switch($_GET['error']) {
                        case 'password_incorrect':
                            echo "Mot de passe incorrect";
                            break;
                        case 'user_not_found':
                            echo "Utilisateur non trouvé";
                            break;
                        case 'role_invalide':
                            echo "Rôle utilisateur invalide";
                            break;
                        default:
                            echo "Erreur de connexion";
                    }
                    ?>
                </div>
            <?php endif; ?>
            
            <form action="login_process.php" method="POST">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="votre@email.com" required>

                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe" required>

                <button type="submit">Se connecter</button>
            </form>
            
            <div class="demo-credentials">
                <p><strong>Compte démo Admin:</strong> admin@softsec.com / admin123</p>
            </div>
        </div>
    </div>
</body>
</html>