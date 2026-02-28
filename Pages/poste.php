<?php
// Pages/poste.php
session_start();
require_once '../config/auth.php';
require_once '../config/post_functions.php';

checkAuth('Admin'); // Seul l'admin peut gérer les postes

$posteManager = new PosteManager();

// Traitement des messages
$message = '';
$messageType = '';

// Vérifier s'il y a des messages dans l'URL
if(isset($_GET['success'])) {
    $message = $_GET['success'];
    $messageType = 'success';
} elseif(isset($_GET['error'])) {
    $message = $_GET['error'];
    $messageType = 'error';
}

// Récupérer tous les postes
$postes = $posteManager->getAllPostes();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Gestion des Postes</title>
    <style>
        .message {
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .btn-modifier {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-supprimer {
            background-color: #dc3545;
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
            margin-left: 5px;
        }
        .btn-modifier:hover {
            background-color: #218838;
        }
        .btn-supprimer:hover {
            background-color: #c82333;
        }
        .user-info {
            float: right;
            font-size: 14px;
            color: #666;
        }
        .btn-deconnexion {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            margin-left: 10px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: left;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        table tr:hover {
            background-color: #f5f5f5;
        }
        .nb-employes {
            background-color: #17a2b8;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Postes</h1>
        <div class="user-info">
            Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php">Dashboard</a>
        <a href="GestionE.php">Employés</a>
        <a href="poste.php" class="active">Postes</a>
        <a href="contract.php">Contrats</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
    </nav>

    <div class="container">
        <?php if($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Ajouter un Poste</h2>
            <form method="POST" action="ajouter_poste.php">
                <label>Libellé du poste</label>
                <input type="text" name="libelle" placeholder="ex: Développeur Full Stack" required>

                <label>Département</label>
                <select name="departement" required>
                    <option value="">--Sélectionner--</option>
                    <option value="DSI">DSI</option>
                    <option value="RH">RH</option>
                    <option value="MARKETING">Marketing</option>
                    <option value="COMMERCIAL">Commercial</option>
                    <option value="ADMINISTRATION">Administration</option>
                    <option value="DIRECTION">Direction</option>
                </select>

                <label>Salaire de base (FCFA)</label>
                <input type="number" name="salairebase" min="0" step="1000" placeholder="ex: 400000" required>

                <label>Description (optionnelle)</label>
                <textarea name="description" rows="3" placeholder="Description du poste..."></textarea>

                <button type="submit">Ajouter le poste</button>
            </form>
        </div>

        <div class="card">
            <h2>Liste des Postes</h2>
            
            <?php if(empty($postes)): ?>
                <p style="text-align: center; color: #666;">Aucun poste enregistré</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Libellé</th>
                            <th>Département</th>
                            <th>Salaire de base</th>
                            <th>Employés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($postes as $poste): 
                            $nbEmployes = $posteManager->getEmployesCountByPoste($poste['idposte']);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($poste['libelle']); ?></td>
                            <td><?php echo htmlspecialchars($poste['departement'] ?: 'Non défini'); ?></td>
                            <td><?php echo number_format($poste['salairebase'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <span class="nb-employes"><?php echo $nbEmployes; ?> employé(s)</span>
                            </td>
                            <td>
                                <a href="modifier_poste.php?id=<?php echo $poste['idposte']; ?>" class="btn-modifier">Modifier</a>
                                <a href="supprimer_poste.php?id=<?php echo $poste['idposte']; ?>" 
                                   class="btn-supprimer" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce poste ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>