<?php
// Pages/DashboardAdmin.php
session_start();
require_once '../config/auth.php';
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté et est admin
checkAuth('Admin');

// Créer une instance de la base de données
$database = new Database();
$db = $database->getConnection();

// Fonction pour récupérer le total des employés actifs
function getTotalEmployes($db) {
    $query = "SELECT COUNT(*) as total FROM employee WHERE statut = 'Actif'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Fonction pour récupérer les congés en attente
function getCongesEnAttente($db) {
    $query = "SELECT COUNT(*) as total FROM conge WHERE etat = 'En attente'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Fonction pour récupérer les paies du mois en cours
function getPaiesMois($db) {
    $mois = date('m');
    $annee = date('Y');
    
    $query = "SELECT COUNT(*) as total, SUM(montantnet) as total_montant 
              FROM paie WHERE mois = :mois AND annee = :annee";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':mois', $mois);
    $stmt->bindParam(':annee', $annee);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les contrats actifs
function getContratsActifs($db) {
    $query = "SELECT COUNT(*) as total FROM contrat WHERE statut = 'Actif'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

// Fonction pour récupérer les employés par département
function getEmployesParDepartement($db) {
    $query = "SELECT p.departement, COUNT(e.matricule) as total 
              FROM employee e 
              INNER JOIN poste p ON e.idposte = p.idposte 
              WHERE e.statut = 'Actif'
              GROUP BY p.departement";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer les prochains congés
function getProchainsConges($db) {
    $query = "SELECT c.*, e.nom, e.prenom 
              FROM conge c 
              INNER JOIN employee e ON c.matricule = e.matricule 
              WHERE c.etat = 'Approuvé' AND c.datedebut >= CURDATE() 
              ORDER BY c.datedebut ASC 
              LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer toutes les statistiques
$totalEmployes = getTotalEmployes($db);
$congesAttente = getCongesEnAttente($db);
$paiesMois = getPaiesMois($db);
$contratsActifs = getContratsActifs($db);
$employesParDepartement = getEmployesParDepartement($db);
$prochainsConges = getProchainsConges($db);

// Calculer le total des salaires du mois
$totalSalairesMois = $paiesMois['total_montant'] ?? 0;
$nombrePaiesMois = $paiesMois['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Dashboard Admin - Gestion RH</title>
    <style>
        .dashboard-container {
            padding: 20px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .stat-box h2 {
            font-size: 36px;
            margin: 0;
            color: #333;
        }
        
        .stat-box p {
            font-size: 16px;
            color: #666;
            margin: 10px 0 0;
        }
        
        .stat-box.employees { border-top: 4px solid #007bff; }
        .stat-box.conges { border-top: 4px solid #ffc107; }
        .stat-box.paies { border-top: 4px solid #28a745; }
        .stat-box.contrats { border-top: 4px solid #17a2b8; }
        
        .dashboard-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 30px;
        }
        
        .dashboard-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .dashboard-card h3 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        
        .department-list {
            list-style: none;
            padding: 0;
        }
        
        .department-list li {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .department-list li:last-child {
            border-bottom: none;
        }
        
        .department-name {
            font-weight: bold;
            color: #555;
        }
        
        .department-count {
            background: #007bff;
            color: white;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 14px;
        }
        
        .conges-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .conges-table th {
            text-align: left;
            padding: 10px;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        .conges-table td {
            padding: 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }
        
        .quick-action-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
        }
        
        .quick-action-btn:hover {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        
        .total-salaire {
            font-size: 24px;
            color: #28a745;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .dashboard-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Tableau de bord - Administrateur RH</h1>
        <div class="user-info">
            Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?> 
            (<?php echo htmlspecialchars($_SESSION['role']); ?>)
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
    </nav>

    <div class="dashboard-container">
        <!-- Statistiques principales -->
        <div class="stats">
            <div class="stat-box employees">
                <h2><?php echo $totalEmployes; ?></h2>
                <p>Employés actifs</p>
                <small>Dernière mise à jour: <?php echo date('d/m/Y H:i'); ?></small>
            </div>
            <div class="stat-box conges">
                <h2><?php echo $congesAttente; ?></h2>
                <p>Congés en attente</p>
                <a href="conge.php?filter=attente" style="color: #ffc107; text-decoration: none;">Voir les demandes</a>
            </div>
            <div class="stat-box paies">
                <h2><?php echo $nombrePaiesMois; ?></h2>
                <p>Paies générées (<?php echo date('F Y'); ?>)</p>
                <div class="total-salaire"><?php echo number_format($totalSalairesMois, 0, ',', ' '); ?> FCFA</div>
            </div>
            <div class="stat-box contrats">
                <h2><?php echo $contratsActifs; ?></h2>
                <p>Contrats actifs</p>
                <small><?php echo round(($contratsActifs / max($totalEmployes, 1)) * 100, 1); ?>% des employés</small>
            </div>
        </div>

        <!-- Deuxième ligne : graphiques et listes -->
        <div class="dashboard-row">
            <!-- Répartition par département -->
            <div class="dashboard-card">
                <h3>Répartition des employés par département</h3>
                <ul class="department-list">
                    <?php if(empty($employesParDepartement)): ?>
                        <li>Aucun employé trouvé</li>
                    <?php else: ?>
                        <?php foreach($employesParDepartement as $dept): ?>
                            <li>
                                <span class="department-name">
                                    <?php echo htmlspecialchars($dept['departement'] ?: 'Non assigné'); ?>
                                </span>
                                <span class="department-count">
                                    <?php echo $dept['total']; ?> employé(s)
                                </span>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Prochains congés -->
            <div class="dashboard-card">
                <h3>Prochains congés (5 prochains)</h3>
                <?php if(empty($prochainsConges)): ?>
                    <p>Aucun congé prévu</p>
                <?php else: ?>
                    <table class="conges-table">
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Début</th>
                                <th>Fin</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($prochainsConges as $conge): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($conge['prenom'] . ' ' . $conge['nom']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($conge['datedebut'])); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($conge['datefin'])); ?></td>
                                    <td>
                                        <span class="badge badge-success">Approuvé</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Troisième ligne : Actions rapides -->
        <div class="dashboard-card" style="margin-top: 20px;">
            <h3>Actions rapides</h3>
            <div class="quick-actions">
                <a href="GestionE.php?action=add" class="quick-action-btn">➕ Nouvel employé</a>
                <a href="conge.php?action=add" class="quick-action-btn">📅 Nouveau congé</a>
                <a href="paie.php?action=generate" class="quick-action-btn">💰 Générer paie</a>
                <a href="contract.php?action=add" class="quick-action-btn">📄 Nouveau contrat</a>
                <a href="poste.php?action=add" class="quick-action-btn">💼 Nouveau poste</a>
            </div>
        </div>
    </div>

    <!-- Script pour actualiser les données (optionnel) -->
    <script>
    // Actualiser les données toutes les 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000); // 300000 ms = 5 minutes
    </script>
</body>
</html>