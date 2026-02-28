<?php
// Pages/contract.php
session_start();
require_once '../config/auth.php';
require_once '../config/contract_functions.php';

checkAuth('Admin');

$contratManager = new ContratManager();

// Traitement des messages
$message = '';
$messageType = '';

if(isset($_GET['success'])) {
    $message = $_GET['success'];
    $messageType = 'success';
} elseif(isset($_GET['error'])) {
    $message = $_GET['error'];
    $messageType = 'error';
}

// Récupérer tous les contrats
$contrats = $contratManager->getAllContrats();
$stats = $contratManager->getStatsContrats();

// Récupérer la liste des employés pour le select
$db = (new Database())->getConnection();
$queryEmployes = "SELECT matricule, nom, prenom FROM employee WHERE statut = 'Actif' ORDER BY nom";
$stmtEmployes = $db->prepare($queryEmployes);
$stmtEmployes->execute();
$employes = $stmtEmployes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style1.css">
    <title>Gestion des Contrats</title>
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
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-mini-box {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
            border-left: 3px solid #007bff;
        }
        .stat-mini-box h4 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }
        .stat-mini-box p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-actif {
            background-color: #28a745;
            color: white;
        }
        .badge-termine {
            background-color: #6c757d;
            color: white;
        }
        .badge-resilie {
            background-color: #dc3545;
            color: white;
        }
        .badge-renouvele {
            background-color: #17a2b8;
            color: white;
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
        }
        .btn-resilier {
            background-color: #ffc107;
            color: #000;
            padding: 3px 8px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-modifier:hover {
            background-color: #218838;
        }
        .btn-supprimer:hover {
            background-color: #c82333;
        }
        .btn-resilier:hover {
            background-color: #e0a800;
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
        .employe-info {
            font-weight: bold;
            color: #007bff;
        }
        .employe-email {
            font-size: 12px;
            color: #666;
        }
        .date-expiree {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Contrats des Employés</h1>
        <div class="user-info">
            Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?>
            <a href="../logout.php" class="btn-deconnexion">Déconnexion</a>
        </div>
    </header>

    <nav>
        <a href="DashboardAdmin.php">Dashboard</a>
        <a href="GestionE.php">Employés</a>
        <a href="poste.php">Postes</a>
        <a href="contract.php" class="active">Contrats</a>
        <a href="conge.php">Congés</a>
        <a href="paie.php">Paie</a>
    </nav>

    <div class="container">
        <!-- Mini statistiques -->
        <div class="stats-mini">
            <div class="stat-mini-box">
                <h4><?php echo $stats['total']; ?></h4>
                <p>Total contrats</p>
            </div>
            <div class="stat-mini-box">
                <h4><?php 
                    $actifs = array_filter($stats['par_statut'] ?? [], function($s) {
                        return $s['statut'] == 'Actif';
                    });
                    echo !empty($actifs) ? reset($actifs)['total'] : 0;
                ?></h4>
                <p>Contrats actifs</p>
            </div>
            <div class="stat-mini-box">
                <h4><?php echo $stats['expirant_bientot'] ?? 0; ?></h4>
                <p>Expirent bientôt</p>
            </div>
        </div>

        <?php if($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- FORMULAIRE AJOUT CONTRAT -->
        <div class="card">
            <h2>Ajouter un Contrat</h2>
            <form method="POST" action="ajouter_contrat.php">
                <label>Employé</label>
                <select name="matricule" required>
                    <option value="">-- Sélectionner un employé --</option>
                    <?php foreach($employes as $emp): ?>
                        <option value="<?php echo $emp['matricule']; ?>">
                            <?php echo htmlspecialchars($emp['nom'] . ' ' . $emp['prenom']); ?> (Mat: <?php echo $emp['matricule']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <label>Type de Contrat</label>
                <select name="typecontrat" required>
                    <option value="">--Sélectionner--</option>
                    <option value="CDI">CDI (Contrat à Durée Indéterminée)</option>
                    <option value="CDD">CDD (Contrat à Durée Déterminée)</option>
                    <option value="Stage">Stage</option>
                    <option value="Prestation">Prestation</option>
                    <option value="Alternance">Alternance</option>
                </select>

                <label>Date de début</label>
                <input type="date" name="datedebut" value="<?php echo date('Y-m-d'); ?>" required>

                <label>Date de fin <small>(optionnelle pour CDI)</small></label>
                <input type="date" name="datefin">

                <label>Salaire de base (FCFA)</label>
                <input type="number" name="salairebase" step="1000" min="0" required>

                <button type="submit">Ajouter le contrat</button>
            </form>
        </div>

        <!-- TABLEAU CONTRATS EXISTANTS -->
        <div class="card">
            <h2>Liste des Contrats</h2>
            
            <?php if(empty($contrats)): ?>
                <p style="text-align: center; color: #666;">Aucun contrat enregistré</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employé</th>
                            <th>Type contrat</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Salaire</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($contrats as $contrat): 
                            $aujourdhui = date('Y-m-d');
                            $estExpire = $contrat['datefin'] && $contrat['datefin'] < $aujourdhui;
                        ?>
                        <tr>
                            <td>
                                <span class="employe-info">
                                    <?php echo htmlspecialchars($contrat['prenom'] . ' ' . $contrat['nom']); ?>
                                </span>
                                <br>
                                <span class="employe-email">
                                    Mat: <?php echo $contrat['matricule']; ?>
                                </span>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($contrat['typecontrat']); ?></strong>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($contrat['datedebut'])); ?></td>
                            <td class="<?php echo $estExpire ? 'date-expiree' : ''; ?>">
                                <?php echo $contrat['datefin'] ? date('d/m/Y', strtotime($contrat['datefin'])) : 'N/A'; ?>
                            </td>
                            <td><?php echo number_format($contrat['salairebase'], 0, ',', ' '); ?> FCFA</td>
                            <td>
                                <?php
                                $badgeClass = '';
                                switch($contrat['statut']) {
                                    case 'Actif':
                                        $badgeClass = 'badge-actif';
                                        break;
                                    case 'Terminé':
                                        $badgeClass = 'badge-termine';
                                        break;
                                    case 'Résilié':
                                        $badgeClass = 'badge-resilie';
                                        break;
                                    case 'Renouvelé':
                                        $badgeClass = 'badge-renouvele';
                                        break;
                                }
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo htmlspecialchars($contrat['statut']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="modifier_contrat.php?id=<?php echo $contrat['idcontrat']; ?>" class="btn-modifier">Modifier</a>
                                
                                <?php if($contrat['statut'] == 'Actif'): ?>
                                    <a href="resilier_contrat.php?id=<?php echo $contrat['idcontrat']; ?>" 
                                       class="btn-resilier"
                                       onclick="return confirm('Êtes-vous sûr de vouloir résilier ce contrat ?')">Résilier</a>
                                <?php endif; ?>
                                
                                <a href="supprimer_contrat.php?id=<?php echo $contrat['idcontrat']; ?>" 
                                   class="btn-supprimer"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce contrat ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script>
    // Script pour gérer l'affichage conditionnel de la date de fin
    document.querySelector('select[name="typecontrat"]').addEventListener('change', function() {
        const dateFinInput = document.querySelector('input[name="datefin"]');
        if(this.value === 'CDI') {
            dateFinInput.required = false;
            dateFinInput.disabled = true;
            dateFinInput.value = '';
        } else {
            dateFinInput.required = true;
            dateFinInput.disabled = false;
        }
    });
    </script>
</body>
</html>