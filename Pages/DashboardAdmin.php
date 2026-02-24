<?php
// Pages/DashboardAdmin.php
session_start();
require_once '../config/auth.php'; // Fichier de vérification d'authentification

// Vérifier si l'utilisateur est connecté et est admin
checkAuth('Admin');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Dashboard Admin - Gestion RH</title>
</head>
<body>
    <header>
        <h1>Tableau de bord - Administrateur RH</h1>
        <div class="user-info">
            Bienvenue, <?php echo $_SESSION['prenom'] . ' ' . $_SESSION['nom']; ?> 
            (<?php echo $_SESSION['role']; ?>)
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php" class="active">Dashboard</a>
        <a href="GestionE.php">Employés</a>
        <a href="poste.php">Postes</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
        <a href="contract.php">Contrats</a>
        <a href="utilisateurs.php">Utilisateurs</a>
        <a href="statistiques.php">Statistiques</a>
    </nav>

    <div class="container">
        <div class="stats">
            <div class="stat-box">
                <h2 id="total-employes">0</h2>
                <p>Employés</p>
            </div>
            <div class="stat-box">
                <h2 id="conges-attente">0</h2>
                <p>Congés en attente</p>
            </div>
            <div class="stat-box">
                <h2 id="paies-mois">0</h2>
                <p>Paies du mois</p>
            </div>
            <div class="stat-box">
                <h2 id="contrats-actifs">0</h2>
                <p>Contrats actifs</p>
            </div>
        </div>
    </div>

    <script src="../js/dashboard.js"></script>
</body>
</html>